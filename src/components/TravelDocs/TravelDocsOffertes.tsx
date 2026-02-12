import { useState, useEffect } from 'react';
import { FileText, Plus, Search, Filter, Eye, Copy, Trash2, Send, Clock, CheckCircle, AlertCircle, Loader2 } from 'lucide-react';
import { Offerte } from '../../types/offerte';
import { OfferteEditor } from './OfferteEditor';
import { offerteService } from '../../lib/offerteService';
import { useAuth } from '../../contexts/AuthContext';

export function TravelDocsOffertes() {
  const { user } = useAuth();
  const [view, setView] = useState<'list' | 'editor'>('list');
  const [offertes, setOffertes] = useState<Offerte[]>([]);
  const [editingOfferte, setEditingOfferte] = useState<Offerte | undefined>(undefined);
  const [searchQuery, setSearchQuery] = useState('');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const loadOffertes = async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await offerteService.list(user?.brand_id || undefined);
      setOffertes(data);
    } catch (err: any) {
      console.error('Error loading offertes:', err);
      setError(err.message || 'Kon offertes niet laden');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadOffertes();
  }, [user?.brand_id]);

  const handleNewOfferte = () => {
    setEditingOfferte(undefined);
    setView('editor');
  };

  const handleEditOfferte = (offerte: Offerte) => {
    setEditingOfferte(offerte);
    setView('editor');
  };

  const handleSaveOfferte = async (offerte: Offerte, stayInEditor = false) => {
    try {
      setSaving(true);
      // Attach brand_id and agent_id if not set
      if (!offerte.brand_id && user?.brand_id) {
        offerte.brand_id = user.brand_id;
      }
      if (!offerte.agent_id && user?.role === 'agent') {
        offerte.agent_id = user.id;
      }
      const saved = await offerteService.save(offerte);
      setOffertes(prev => {
        const existing = prev.findIndex(o => o.id === saved.id);
        if (existing >= 0) {
          const updated = [...prev];
          updated[existing] = saved;
          return updated;
        }
        return [saved, ...prev];
      });
      // Update the editing offerte with the saved data (including new ID for new offertes)
      setEditingOfferte(saved);
      // Only close editor if not staying in editor (e.g., for preview)
      if (!stayInEditor) {
        setView('list');
      }
    } catch (err: any) {
      console.error('Error saving offerte:', err);
      alert('Fout bij opslaan: ' + (err.message || 'Onbekende fout'));
      throw err; // Re-throw so preview can handle the error
    } finally {
      setSaving(false);
    }
  };

  const handleDeleteOfferte = async (id: string) => {
    if (confirm('Weet je zeker dat je deze offerte wilt verwijderen?')) {
      try {
        await offerteService.delete(id);
        setOffertes(prev => prev.filter(o => o.id !== id));
      } catch (err: any) {
        console.error('Error deleting offerte:', err);
        alert('Fout bij verwijderen: ' + (err.message || 'Onbekende fout'));
      }
    }
  };

  const handleDuplicateOfferte = async (offerte: Offerte) => {
    try {
      const duplicated = await offerteService.duplicate(offerte);
      setOffertes(prev => [duplicated, ...prev]);
    } catch (err: any) {
      console.error('Error duplicating offerte:', err);
      alert('Fout bij dupliceren: ' + (err.message || 'Onbekende fout'));
    }
  };

  const getStatusConfig = (status: Offerte['status']) => {
    switch (status) {
      case 'draft': return { label: 'Concept', icon: Clock, color: 'text-gray-500', bg: 'bg-gray-100' };
      case 'sent': return { label: 'Verstuurd', icon: Send, color: 'text-blue-600', bg: 'bg-blue-50' };
      case 'viewed': return { label: 'Bekeken', icon: Eye, color: 'text-purple-600', bg: 'bg-purple-50' };
      case 'accepted': return { label: 'Geaccepteerd', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-50' };
      case 'revised': return { label: 'Herzien', icon: AlertCircle, color: 'text-orange-600', bg: 'bg-orange-50' };
      case 'expired': return { label: 'Verlopen', icon: AlertCircle, color: 'text-red-600', bg: 'bg-red-50' };
      default: return { label: status, icon: Clock, color: 'text-gray-500', bg: 'bg-gray-100' };
    }
  };

  const filteredOffertes = offertes.filter(o =>
    !searchQuery ||
    o.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
    o.client_name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  // Editor view
  if (view === 'editor') {
    return (
      <OfferteEditor
        offerte={editingOfferte}
        onBack={() => setView('list')}
        onSave={handleSaveOfferte}
      />
    );
  }

  // List view
  return (
    <div className="p-6">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-2xl font-bold text-gray-900 flex items-center gap-3">
              <div className="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <FileText className="w-5 h-5 text-blue-600" />
              </div>
              Offertes
            </h1>
            <p className="text-gray-500 mt-1">Maak professionele reisoffertes voor je klanten</p>
          </div>
          <button
            onClick={handleNewOfferte}
            className="flex items-center gap-2 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium transition-colors shadow-sm"
          >
            <Plus size={18} />
            Nieuwe Offerte
          </button>
        </div>

        {/* Search & Filter Bar */}
        <div className="flex items-center gap-3 mb-6">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={e => setSearchQuery(e.target.value)}
              placeholder="Zoek offertes op klantnaam, bestemming..."
              className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-200 focus:border-orange-400 outline-none text-sm"
            />
          </div>
          <button className="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 text-sm">
            <Filter size={16} />
            Filter
          </button>
        </div>

        {/* Loading state */}
        {loading && (
          <div className="flex items-center justify-center py-20">
            <Loader2 className="w-8 h-8 text-orange-500 animate-spin" />
          </div>
        )}

        {/* Error state */}
        {error && !loading && (
          <div className="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700">
            {error}
            <button onClick={loadOffertes} className="ml-3 underline hover:no-underline">Opnieuw proberen</button>
          </div>
        )}

        {/* Offertes list */}
        {!loading && filteredOffertes.length > 0 ? (
          <div className="space-y-3">
            {filteredOffertes.map(offerte => {
              const statusConfig = getStatusConfig(offerte.status);
              const StatusIcon = statusConfig.icon;
              return (
                <div
                  key={offerte.id}
                  className="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all cursor-pointer"
                  onClick={() => handleEditOfferte(offerte)}
                >
                  <div className="flex items-center p-4">
                    {/* Hero thumbnail */}
                    <div className="w-20 h-14 rounded-lg overflow-hidden bg-gray-100 shrink-0 mr-4">
                      {offerte.hero_image_url ? (
                        <img src={offerte.hero_image_url} alt="" className="w-full h-full object-cover" />
                      ) : (
                        <div className="w-full h-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center">
                          <FileText size={16} className="text-white/40" />
                        </div>
                      )}
                    </div>

                    {/* Content */}
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center gap-2">
                        <h3 className="font-semibold text-gray-900 truncate">{offerte.title || 'Naamloze offerte'}</h3>
                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${statusConfig.bg} ${statusConfig.color}`}>
                          <StatusIcon size={10} />
                          {statusConfig.label}
                        </span>
                      </div>
                      <div className="flex items-center gap-3 text-sm text-gray-500 mt-0.5">
                        <span>{offerte.client_name || 'Geen klant'}</span>
                        <span>·</span>
                        <span>{offerte.items.length} items</span>
                        <span>·</span>
                        <span className="font-medium text-gray-700">€ {(offerte.total_price || 0).toLocaleString('nl-NL', { minimumFractionDigits: 2 })}</span>
                      </div>
                    </div>

                    {/* Date */}
                    <div className="text-xs text-gray-400 shrink-0 mx-4">
                      {new Date(offerte.updated_at).toLocaleDateString('nl-NL', { day: 'numeric', month: 'short', year: 'numeric' })}
                    </div>

                    {/* Actions */}
                    <div className="flex items-center gap-1 shrink-0" onClick={e => e.stopPropagation()}>
                      <button
                        onClick={() => handleDuplicateOfferte(offerte)}
                        className="p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-gray-600"
                        title="Dupliceren"
                      >
                        <Copy size={16} />
                      </button>
                      <button
                        onClick={() => handleDeleteOfferte(offerte.id)}
                        className="p-2 hover:bg-red-50 rounded-lg transition-colors text-gray-400 hover:text-red-500"
                        title="Verwijderen"
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        ) : !loading ? (
          /* Empty State */
          <div className="bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div className="flex flex-col items-center justify-center py-20 px-6 text-center">
              <div className="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mb-6">
                <FileText className="w-10 h-10 text-blue-400" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">Nog geen offertes</h3>
              <p className="text-gray-500 max-w-md mb-6">
                Maak je eerste professionele reisofferte. Kies een reis, personaliseer de inhoud en verstuur direct naar je klant.
              </p>
              <button
                onClick={handleNewOfferte}
                className="flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium transition-colors"
              >
                <Plus size={18} />
                Maak je eerste offerte
              </button>
            </div>
          </div>
        ) : null}
      </div>
    </div>
  );
}
