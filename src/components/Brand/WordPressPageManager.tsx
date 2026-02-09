import React, { useState, useEffect } from 'react';
import { supabase } from '../../lib/supabase';
import { useAuth } from '../../contexts/AuthContext';
import { RefreshCw, Edit, Eye, FileText, CheckCircle, Clock, MessageSquare, Send, X, Loader2, PenTool, AlertCircle, ChevronDown, ChevronUp } from 'lucide-react';

interface WordPressPage {
  id: string;
  wordpress_page_id: number;
  title: string;
  slug: string;
  page_url: string;
  edit_url: string;
  elementor_edit_url: string;
  status: string;
  modified_at: string;
  last_synced_at: string;
}

interface ChangeRequest {
  id: string;
  page_id: string | null;
  page_title: string;
  request_type: 'change' | 'new_page' | 'delete' | 'other';
  description: string;
  priority: 'low' | 'normal' | 'high' | 'urgent';
  status: 'new' | 'in_progress' | 'completed' | 'rejected';
  admin_notes: string | null;
  created_at: string;
  completed_at: string | null;
}

export default function WordPressPageManager() {
  const { effectiveBrandId, user } = useAuth();
  const [pages, setPages] = useState<WordPressPage[]>([]);
  const [changeRequests, setChangeRequests] = useState<ChangeRequest[]>([]);
  const [loading, setLoading] = useState(true);
  const [syncing, setSyncing] = useState(false);
  const [lastSyncTime, setLastSyncTime] = useState<string | null>(null);
  const [showRequestModal, setShowRequestModal] = useState(false);
  const [selectedPageForRequest, setSelectedPageForRequest] = useState<WordPressPage | null>(null);
  const [requestForm, setRequestForm] = useState({ description: '', priority: 'normal' as string, request_type: 'change' as string });
  const [submittingRequest, setSubmittingRequest] = useState(false);
  const [showRequests, setShowRequests] = useState(false);
  const [activeTab, setActiveTab] = useState<'pages' | 'requests'>('pages');

  const loadPages = async () => {
    if (!effectiveBrandId) {
      setLoading(false);
      return;
    }

    setLoading(true);
    try {
      const { data, error } = await supabase
        .from('wordpress_pages_cache')
        .select('*')
        .eq('brand_id', effectiveBrandId)
        .order('title', { ascending: true });

      if (error) {
        console.error('Error loading pages:', error);
      } else {
        setPages(data || []);
        if (data && data.length > 0) {
          setLastSyncTime(data[0].last_synced_at);
        }
      }
    } catch (err) {
      console.error('Exception loading pages:', err);
    } finally {
      setLoading(false);
    }
  };

  const loadChangeRequests = async () => {
    if (!effectiveBrandId) return;
    try {
      const { data, error } = await supabase
        .from('wordpress_change_requests')
        .select('*')
        .eq('brand_id', effectiveBrandId)
        .order('created_at', { ascending: false });

      if (!error) {
        setChangeRequests(data || []);
      }
    } catch (err) {
      console.error('Error loading change requests:', err);
    }
  };

  useEffect(() => {
    if (effectiveBrandId) {
      loadPages();
      loadChangeRequests();
    } else {
      setLoading(false);
    }
  }, [effectiveBrandId]);

  const syncPages = async () => {
    setSyncing(true);
    try {
      const { data: { session } } = await supabase.auth.getSession();
      if (!session) {
        alert('Niet ingelogd');
        setSyncing(false);
        return;
      }

      const response = await fetch(
        `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/wordpress-sync-pages`,
        {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${session.access_token}`,
            'Content-Type': 'application/json',
          },
        }
      );

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to sync pages');
      }

      const result = await response.json();
      await loadPages();
      alert(`Succesvol ${result.pages_synced} pagina's gesynchroniseerd!`);
    } catch (error: any) {
      console.error('Sync error:', error);
      alert(error.message || 'Fout bij synchroniseren. Controleer of je WordPress gegevens correct zijn ingevuld.');
    }
    setSyncing(false);
  };

  const openRequestModal = (page?: WordPressPage) => {
    setSelectedPageForRequest(page || null);
    setRequestForm({
      description: '',
      priority: 'normal',
      request_type: page ? 'change' : 'new_page',
    });
    setShowRequestModal(true);
  };

  const submitChangeRequest = async () => {
    if (!effectiveBrandId || !requestForm.description.trim()) return;
    setSubmittingRequest(true);

    try {
      const { error } = await supabase
        .from('wordpress_change_requests')
        .insert({
          brand_id: effectiveBrandId,
          page_id: selectedPageForRequest?.id || null,
          page_title: selectedPageForRequest?.title || 'Nieuwe pagina / Algemeen',
          request_type: requestForm.request_type,
          description: requestForm.description.trim(),
          priority: requestForm.priority,
          status: 'new',
          requested_by: user?.id,
        });

      if (error) throw error;

      setShowRequestModal(false);
      await loadChangeRequests();
      alert('Wijzigingsverzoek ingediend! We nemen zo snel mogelijk contact op.');
    } catch (err: any) {
      console.error('Error submitting change request:', err);
      alert('Fout bij indienen verzoek: ' + (err.message || 'Onbekende fout'));
    } finally {
      setSubmittingRequest(false);
    }
  };

  const getStatusBadge = (status: string) => {
    const config: Record<string, { label: string; color: string; icon: any }> = {
      publish: { label: 'Gepubliceerd', color: 'bg-green-100 text-green-800', icon: CheckCircle },
      draft: { label: 'Concept', color: 'bg-gray-100 text-gray-800', icon: FileText },
      pending: { label: 'In Review', color: 'bg-yellow-100 text-yellow-800', icon: Clock },
      private: { label: 'Privé', color: 'bg-blue-100 text-blue-800', icon: Eye },
    };
    const { label, color, icon: Icon } = config[status] || config.draft;
    return (
      <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${color}`}>
        <Icon className="w-3 h-3" />
        {label}
      </span>
    );
  };

  const getRequestStatusBadge = (status: string) => {
    const config: Record<string, { label: string; color: string }> = {
      new: { label: 'Nieuw', color: 'bg-orange-100 text-orange-800' },
      in_progress: { label: 'In behandeling', color: 'bg-blue-100 text-blue-800' },
      completed: { label: 'Afgerond', color: 'bg-green-100 text-green-800' },
      rejected: { label: 'Afgewezen', color: 'bg-red-100 text-red-800' },
    };
    const { label, color } = config[status] || config.new;
    return <span className={`px-2 py-1 rounded-full text-xs font-medium ${color}`}>{label}</span>;
  };

  const getRequestTypeBadge = (type: string) => {
    const config: Record<string, { label: string; color: string }> = {
      change: { label: 'Wijziging', color: 'bg-purple-100 text-purple-800' },
      new_page: { label: 'Nieuwe pagina', color: 'bg-teal-100 text-teal-800' },
      delete: { label: 'Verwijderen', color: 'bg-red-100 text-red-800' },
      other: { label: 'Overig', color: 'bg-gray-100 text-gray-800' },
    };
    const { label, color } = config[type] || config.other;
    return <span className={`px-2 py-0.5 rounded text-xs font-medium ${color}`}>{label}</span>;
  };

  const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleString('nl-NL', {
      day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
  };

  const pendingRequestsCount = changeRequests.filter(r => r.status === 'new' || r.status === 'in_progress').length;

  if (loading) {
    return <div className="p-8 flex items-center gap-2"><Loader2 className="w-5 h-5 animate-spin" /> Laden...</div>;
  }

  return (
    <div className="p-8">
      {/* Header */}
      <div className="mb-6">
        <div className="flex items-center justify-between mb-2">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">WordPress Pagina Beheer</h1>
            <p className="text-gray-600 text-sm">Beheer je pagina's en vraag wijzigingen aan</p>
          </div>
          <div className="flex items-center gap-2">
            <button
              onClick={() => openRequestModal()}
              className="flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 text-sm"
            >
              <MessageSquare className="w-4 h-4" />
              Wijziging aanvragen
            </button>
            <button
              onClick={syncPages}
              disabled={syncing}
              className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 text-sm"
            >
              <RefreshCw className={`w-4 h-4 ${syncing ? 'animate-spin' : ''}`} />
              {syncing ? 'Synchroniseren...' : 'Sync pagina\'s'}
            </button>
          </div>
        </div>
        {lastSyncTime && (
          <p className="text-xs text-gray-400">Laatste sync: {formatDate(lastSyncTime)}</p>
        )}
      </div>

      {/* Tabs */}
      <div className="flex gap-1 mb-6 border-b">
        <button
          onClick={() => setActiveTab('pages')}
          className={`px-4 py-2 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'pages' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'
          }`}
        >
          Pagina's ({pages.length})
        </button>
        <button
          onClick={() => setActiveTab('requests')}
          className={`px-4 py-2 text-sm font-medium border-b-2 transition-colors flex items-center gap-1 ${
            activeTab === 'requests' ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700'
          }`}
        >
          Wijzigingsverzoeken
          {pendingRequestsCount > 0 && (
            <span className="bg-orange-500 text-white text-xs px-1.5 py-0.5 rounded-full">{pendingRequestsCount}</span>
          )}
        </button>
      </div>

      {/* Pages Tab */}
      {activeTab === 'pages' && (
        <>
          {pages.length === 0 ? (
            <div className="text-center py-16 bg-gray-50 rounded-lg">
              <FileText className="w-16 h-16 text-gray-300 mx-auto mb-4" />
              <h3 className="text-lg font-medium text-gray-900 mb-2">Geen pagina's gevonden</h3>
              <p className="text-gray-500 mb-4 text-sm">Klik op "Sync pagina's" om je WordPress pagina's in te laden</p>
              <button
                onClick={syncPages}
                disabled={syncing}
                className="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 disabled:opacity-50 text-sm"
              >
                <RefreshCw className={`w-4 h-4 ${syncing ? 'animate-spin' : ''}`} />
                {syncing ? 'Bezig...' : 'Nu synchroniseren'}
              </button>
            </div>
          ) : (
            <div className="bg-white rounded-lg border border-gray-200 overflow-hidden">
              <table className="w-full">
                <thead className="bg-gray-50 border-b">
                  <tr>
                    <th className="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagina</th>
                    <th className="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th className="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acties</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                  {pages.map((page) => (
                    <tr key={page.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-5 py-3">
                        <div className="flex items-center gap-2">
                          <FileText className="w-4 h-4 text-gray-400 flex-shrink-0" />
                          <div>
                            <span className="font-medium text-gray-900 text-sm">{page.title}</span>
                            <span className="text-xs text-gray-400 ml-2 font-mono">/{page.slug}</span>
                          </div>
                        </div>
                      </td>
                      <td className="px-5 py-3">
                        {getStatusBadge(page.status)}
                      </td>
                      <td className="px-5 py-3">
                        <div className="flex items-center justify-end gap-1.5">
                          <a
                            href={page.elementor_edit_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1 px-2.5 py-1.5 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition-colors"
                            title="Bewerken in Elementor"
                          >
                            <PenTool className="w-3.5 h-3.5" />
                            Elementor
                          </a>
                          <a
                            href={page.edit_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors"
                            title="Bewerken in WordPress"
                          >
                            <Edit className="w-3.5 h-3.5" />
                            WP Editor
                          </a>
                          <a
                            href={page.page_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1 px-2.5 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-lg hover:bg-gray-200 transition-colors"
                            title="Bekijk live pagina"
                          >
                            <Eye className="w-3.5 h-3.5" />
                            Bekijken
                          </a>
                          <button
                            onClick={() => openRequestModal(page)}
                            className="inline-flex items-center gap-1 px-2.5 py-1.5 bg-orange-50 text-orange-700 text-xs rounded-lg hover:bg-orange-100 transition-colors"
                            title="Wijziging aanvragen voor deze pagina"
                          >
                            <MessageSquare className="w-3.5 h-3.5" />
                            Aanpassen
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
              <div className="bg-gray-50 px-5 py-3 border-t text-xs text-gray-500">
                {pages.length} pagina{pages.length !== 1 ? "'s" : ''} gevonden
              </div>
            </div>
          )}
        </>
      )}

      {/* Change Requests Tab */}
      {activeTab === 'requests' && (
        <div>
          {changeRequests.length === 0 ? (
            <div className="text-center py-16 bg-gray-50 rounded-lg">
              <MessageSquare className="w-16 h-16 text-gray-300 mx-auto mb-4" />
              <h3 className="text-lg font-medium text-gray-900 mb-2">Geen verzoeken</h3>
              <p className="text-gray-500 text-sm mb-4">Je hebt nog geen wijzigingsverzoeken ingediend</p>
              <button
                onClick={() => openRequestModal()}
                className="inline-flex items-center gap-2 bg-orange-600 text-white px-6 py-2.5 rounded-lg hover:bg-orange-700 text-sm"
              >
                <MessageSquare className="w-4 h-4" />
                Eerste verzoek indienen
              </button>
            </div>
          ) : (
            <div className="space-y-3">
              {changeRequests.map((req) => (
                <div key={req.id} className="bg-white rounded-lg border border-gray-200 p-4">
                  <div className="flex items-start justify-between mb-2">
                    <div className="flex items-center gap-2">
                      {getRequestTypeBadge(req.request_type)}
                      {getRequestStatusBadge(req.status)}
                    </div>
                    <span className="text-xs text-gray-400">{formatDate(req.created_at)}</span>
                  </div>
                  <h4 className="font-medium text-sm text-gray-900 mb-1">{req.page_title}</h4>
                  <p className="text-sm text-gray-600 whitespace-pre-wrap">{req.description}</p>
                  {req.admin_notes && (
                    <div className="mt-3 bg-blue-50 rounded p-3">
                      <p className="text-xs font-medium text-blue-700 mb-1">Reactie van beheerder:</p>
                      <p className="text-sm text-blue-800">{req.admin_notes}</p>
                    </div>
                  )}
                  {req.completed_at && (
                    <p className="text-xs text-gray-400 mt-2">Afgerond: {formatDate(req.completed_at)}</p>
                  )}
                </div>
              ))}
            </div>
          )}
        </div>
      )}

      {/* Change Request Modal */}
      {showRequestModal && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl shadow-xl max-w-lg w-full">
            <div className="flex items-center justify-between p-5 border-b">
              <h3 className="text-lg font-semibold">Wijziging aanvragen</h3>
              <button onClick={() => setShowRequestModal(false)} className="text-gray-400 hover:text-gray-600">
                <X className="w-5 h-5" />
              </button>
            </div>
            <div className="p-5 space-y-4">
              {selectedPageForRequest && (
                <div className="bg-gray-50 rounded-lg p-3">
                  <p className="text-xs text-gray-500">Pagina</p>
                  <p className="font-medium text-sm">{selectedPageForRequest.title}</p>
                </div>
              )}

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Type verzoek</label>
                <select
                  value={requestForm.request_type}
                  onChange={(e) => setRequestForm(prev => ({ ...prev, request_type: e.target.value }))}
                  className="w-full px-3 py-2 border rounded-lg text-sm"
                >
                  <option value="change">Wijziging op bestaande pagina</option>
                  <option value="new_page">Nieuwe pagina aanmaken</option>
                  <option value="delete">Pagina verwijderen</option>
                  <option value="other">Overig</option>
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Prioriteit</label>
                <select
                  value={requestForm.priority}
                  onChange={(e) => setRequestForm(prev => ({ ...prev, priority: e.target.value }))}
                  className="w-full px-3 py-2 border rounded-lg text-sm"
                >
                  <option value="low">Laag — geen haast</option>
                  <option value="normal">Normaal</option>
                  <option value="high">Hoog — graag snel</option>
                  <option value="urgent">Urgent — zo snel mogelijk</option>
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Beschrijving *</label>
                <textarea
                  value={requestForm.description}
                  onChange={(e) => setRequestForm(prev => ({ ...prev, description: e.target.value }))}
                  rows={4}
                  placeholder="Beschrijf wat je wilt laten aanpassen..."
                  className="w-full px-3 py-2 border rounded-lg text-sm"
                />
              </div>
            </div>
            <div className="flex items-center justify-end gap-2 p-5 border-t bg-gray-50 rounded-b-xl">
              <button
                onClick={() => setShowRequestModal(false)}
                className="px-4 py-2 text-sm text-gray-600 hover:text-gray-800"
              >
                Annuleren
              </button>
              <button
                onClick={submitChangeRequest}
                disabled={submittingRequest || !requestForm.description.trim()}
                className="flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 disabled:opacity-50"
              >
                {submittingRequest ? <Loader2 className="w-4 h-4 animate-spin" /> : <Send className="w-4 h-4" />}
                Verzoek indienen
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
