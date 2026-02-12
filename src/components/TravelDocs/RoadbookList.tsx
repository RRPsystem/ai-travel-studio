import { useState, useEffect } from 'react';
import { BookOpen, Plus, Search, Filter, Eye, Copy, Trash2, MapPin, Calendar, Loader2 } from 'lucide-react';
import { TravelDocsRoadbook } from './TravelDocsRoadbook';
import { useAuth } from '../../contexts/AuthContext';
import { db } from '../../lib/supabase';

interface Roadbook {
  id: string;
  brand_id: string;
  client_name: string;
  title: string;
  subtitle?: string;
  created_at: string;
  updated_at: string;
}

export function RoadbookList() {
  const { user } = useAuth();
  const [view, setView] = useState<'list' | 'editor'>('list');
  const [roadbooks, setRoadbooks] = useState<Roadbook[]>([]);
  const [editingRoadbook, setEditingRoadbook] = useState<Roadbook | undefined>(undefined);
  const [searchQuery, setSearchQuery] = useState('');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const loadRoadbooks = async () => {
    if (!db.supabase || !user?.brand_id) return;
    try {
      setLoading(true);
      setError(null);
      const { data, error: dbError } = await db.supabase
        .from('travel_roadbooks')
        .select('*')
        .eq('brand_id', user.brand_id)
        .order('created_at', { ascending: false });
      if (dbError) throw dbError;
      setRoadbooks(data || []);
    } catch (err: any) {
      console.error('Error loading roadbooks:', err);
      setError(err.message || 'Kon roadbooks niet laden');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadRoadbooks();
  }, [user?.brand_id]);

  const handleNewRoadbook = () => {
    setEditingRoadbook(undefined);
    setView('editor');
  };

  const handleEditRoadbook = (roadbook: Roadbook) => {
    setEditingRoadbook(roadbook);
    setView('editor');
  };

  const handleSaveRoadbook = async (roadbook: any, stayInEditor = false) => {
    if (!db.supabase || !user?.brand_id) return;
    try {
      setSaving(true);
      const roadbookData = {
        ...roadbook,
        brand_id: user.brand_id,
        updated_at: new Date().toISOString()
      };
      
      if (roadbook.id) {
        const { data, error: dbError } = await db.supabase
          .from('travel_roadbooks')
          .update(roadbookData)
          .eq('id', roadbook.id)
          .select()
          .single();
        if (dbError) throw dbError;
        setRoadbooks(prev => prev.map(r => r.id === data.id ? data : r));
        setEditingRoadbook(data);
      } else {
        roadbookData.created_at = new Date().toISOString();
        const { data, error: dbError } = await db.supabase
          .from('travel_roadbooks')
          .insert(roadbookData)
          .select()
          .single();
        if (dbError) throw dbError;
        setRoadbooks(prev => [data, ...prev]);
        setEditingRoadbook(data);
      }
      
      if (!stayInEditor) {
        setView('list');
      }
    } catch (err: any) {
      console.error('Error saving roadbook:', err);
      alert('Fout bij opslaan: ' + (err.message || 'Onbekende fout'));
      throw err;
    } finally {
      setSaving(false);
    }
  };

  const handleDeleteRoadbook = async (id: string) => {
    if (!db.supabase) return;
    if (confirm('Weet je zeker dat je dit roadbook wilt verwijderen?')) {
      try {
        const { error: dbError } = await db.supabase
          .from('travel_roadbooks')
          .delete()
          .eq('id', id);
        if (dbError) throw dbError;
        setRoadbooks(prev => prev.filter(r => r.id !== id));
      } catch (err: any) {
        console.error('Error deleting roadbook:', err);
        alert('Fout bij verwijderen: ' + (err.message || 'Onbekende fout'));
      }
    }
  };

  const handleDuplicateRoadbook = async (roadbook: Roadbook) => {
    if (!db.supabase) return;
    try {
      const { data, error: dbError } = await db.supabase
        .from('travel_roadbooks')
        .insert({
          ...roadbook,
          id: undefined,
          title: roadbook.title + ' (kopie)',
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString()
        })
        .select()
        .single();
      if (dbError) throw dbError;
      setRoadbooks(prev => [data, ...prev]);
    } catch (err: any) {
      console.error('Error duplicating roadbook:', err);
      alert('Fout bij dupliceren: ' + (err.message || 'Onbekende fout'));
    }
  };


  const filteredRoadbooks = roadbooks.filter(r =>
    !searchQuery ||
    r.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
    r.client_name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  // Editor view
  if (view === 'editor') {
    return (
      <TravelDocsRoadbook
        offerte={editingRoadbook as any}
        onBack={() => setView('list')}
        onSave={handleSaveRoadbook}
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
              <div className="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                <BookOpen className="w-5 h-5 text-emerald-600" />
              </div>
              Roadbook
            </h1>
            <p className="text-gray-500 mt-1">Maak gedetailleerde reisboekjes voor je klanten</p>
          </div>
          <button
            onClick={handleNewRoadbook}
            className="flex items-center gap-2 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium transition-colors shadow-sm"
          >
            <Plus size={18} />
            Nieuw Roadbook
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
              placeholder="Zoek roadbooks op klantnaam, reis..."
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
            <button onClick={loadRoadbooks} className="ml-3 underline hover:no-underline">Opnieuw proberen</button>
          </div>
        )}

        {/* Roadbooks list */}
        {!loading && filteredRoadbooks.length > 0 ? (
          <div className="space-y-3">
            {filteredRoadbooks.map(roadbook => (
                <div
                  key={roadbook.id}
                  className="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all cursor-pointer"
                  onClick={() => handleEditRoadbook(roadbook)}
                >
                  <div className="flex items-center p-4">
                    {/* Icon */}
                    <div className="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0 mr-4">
                      <BookOpen className="w-6 h-6 text-emerald-600" />
                    </div>

                    {/* Content */}
                    <div className="flex-1 min-w-0">
                      <h3 className="font-semibold text-gray-900 truncate">{roadbook.title || 'Naamloos roadbook'}</h3>
                      <div className="flex items-center gap-3 text-sm text-gray-500 mt-0.5">
                        <span>{roadbook.client_name || 'Geen klant'}</span>
                      </div>
                    </div>

                    {/* Date */}
                    <div className="text-xs text-gray-400 shrink-0 mx-4">
                      {new Date(roadbook.updated_at).toLocaleDateString('nl-NL', { day: 'numeric', month: 'short', year: 'numeric' })}
                    </div>

                    {/* Actions */}
                    <div className="flex items-center gap-1 shrink-0" onClick={e => e.stopPropagation()}>
                      <button
                        onClick={() => handleDuplicateRoadbook(roadbook)}
                        className="p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-gray-600"
                        title="Dupliceren"
                      >
                        <Copy size={16} />
                      </button>
                      <button
                        onClick={() => handleDeleteRoadbook(roadbook.id)}
                        className="p-2 hover:bg-red-50 rounded-lg transition-colors text-gray-400 hover:text-red-500"
                        title="Verwijderen"
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>
                  </div>
                </div>
            ))}
          </div>
        ) : !loading ? (
          /* Empty State */
          <div className="bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div className="flex flex-col items-center justify-center py-20 px-6 text-center">
              <div className="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6">
                <BookOpen className="w-10 h-10 text-emerald-400" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">Nog geen roadbooks</h3>
              <p className="text-gray-500 max-w-md mb-6">
                Maak een compleet reisboekje met dag-tot-dag programma, kaarten, hotel info en praktische tips.
              </p>
              <button
                onClick={handleNewRoadbook}
                className="flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium transition-colors"
              >
                <Plus size={18} />
                Maak je eerste roadbook
              </button>
            </div>
          </div>
        ) : null}
      </div>
    </div>
  );
}
