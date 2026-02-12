import { useState, useEffect } from 'react';
import { BookOpen, Plus, Search, Filter, Map, Car, Calendar, MapPin, Hotel, ChevronRight, Eye, Edit, Trash2, ArrowLeft, Save, GripVertical, X, ChevronDown, ChevronUp } from 'lucide-react';
import { useAuth } from '../../contexts/AuthContext';
import { db } from '../../lib/supabase';

interface RoadbookDay {
  day: number;
  date?: string;
  location: string;
  hotel?: {
    name: string;
    address?: string;
    nights?: number;
  };
  activities: string[];
  description?: string;
}

interface Roadbook {
  id: string;
  brand_id: string;
  client_name: string;
  title: string;
  subtitle?: string;
  days: RoadbookDay[];
  destinations: string[];
  created_at: string;
  updated_at: string;
}

export function TravelDocsRoadbook() {
  const { user } = useAuth();
  const [roadbooks, setRoadbooks] = useState<Roadbook[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [showEditor, setShowEditor] = useState(false);
  const [selectedRoadbook, setSelectedRoadbook] = useState<Roadbook | null>(null);
  const [previewRoadbook, setPreviewRoadbook] = useState<Roadbook | null>(null);

  useEffect(() => {
    loadRoadbooks();
  }, []);

  const loadRoadbooks = async () => {
    if (!db.supabase || !user?.brand_id) return;
    setLoading(true);
    try {
      const { data, error } = await db.supabase
        .from('travel_roadbooks')
        .select('*')
        .eq('brand_id', user.brand_id)
        .order('created_at', { ascending: false });

      if (error) throw error;
      setRoadbooks(data || []);
    } catch (error) {
      console.error('Error loading roadbooks:', error);
    } finally {
      setLoading(false);
    }
  };

  const filteredRoadbooks = roadbooks.filter(rb =>
    rb.client_name.toLowerCase().includes(searchQuery.toLowerCase()) ||
    rb.title.toLowerCase().includes(searchQuery.toLowerCase())
  );

  // Editor mode - inline split screen
  if (showEditor) {
    return (
      <RoadbookEditorInline
        roadbookId={selectedRoadbook?.id}
        onClose={() => {
          setShowEditor(false);
          setSelectedRoadbook(null);
        }}
        onSave={() => {
          loadRoadbooks();
        }}
      />
    );
  }

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
            onClick={() => setShowEditor(true)}
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
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Zoek roadbooks op klantnaam, reis..."
              className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-200 focus:border-orange-400 outline-none text-sm"
            />
          </div>
          <button className="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 text-sm">
            <Filter size={16} />
            Filter
          </button>
        </div>

        {/* Roadbooks List or Empty State */}
        {loading ? (
          <div className="flex items-center justify-center py-20">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
          </div>
        ) : filteredRoadbooks.length > 0 ? (
          <div className="grid gap-4">
            {filteredRoadbooks.map((roadbook) => (
              <div key={roadbook.id} className="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                <div className="p-6">
                  <div className="flex items-start justify-between">
                    <div className="flex-1">
                      <h3 className="text-lg font-semibold text-gray-900 mb-1">{roadbook.title}</h3>
                      <p className="text-sm text-gray-600 mb-3">{roadbook.client_name}</p>
                      <div className="flex items-center gap-4 text-sm text-gray-500">
                        <span className="flex items-center gap-1">
                          <Calendar className="w-4 h-4" />
                          {roadbook.days.length} dagen
                        </span>
                        <span className="flex items-center gap-1">
                          <MapPin className="w-4 h-4" />
                          {roadbook.destinations.length} bestemmingen
                        </span>
                      </div>
                    </div>
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => setPreviewRoadbook(roadbook)}
                        className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        title="Preview"
                      >
                        <Eye className="w-5 h-5" />
                      </button>
                      <button
                        onClick={() => {
                          setSelectedRoadbook(roadbook);
                          setShowEditor(true);
                        }}
                        className="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Bewerken"
                      >
                        <Edit className="w-5 h-5" />
                      </button>
                      <button
                        className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        title="Verwijderen"
                      >
                        <Trash2 className="w-5 h-5" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div className="flex flex-col items-center justify-center py-20 px-6 text-center">
              <div className="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6">
                <BookOpen className="w-10 h-10 text-emerald-400" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">
                {searchQuery ? 'Geen roadbooks gevonden' : 'Nog geen roadbooks'}
              </h3>
              <p className="text-gray-500 max-w-md mb-6">
                {searchQuery 
                  ? 'Probeer een andere zoekopdracht'
                  : 'Maak een compleet reisboekje met dag-tot-dag programma, kaarten, hotel info en praktische tips. Klaar om te printen of digitaal te delen.'
                }
              </p>
              {!searchQuery && (
                <button 
                  onClick={() => setShowEditor(true)}
                  className="flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium transition-colors"
                >
                  <Plus size={18} />
                  Maak je eerste roadbook
                </button>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

// Inline split-screen editor like offerte editor
function RoadbookEditorInline({ roadbookId, onClose, onSave }: { roadbookId?: string; onClose: () => void; onSave: () => void }) {
  const { user } = useAuth();
  const [title, setTitle] = useState('');
  const [subtitle, setSubtitle] = useState('');
  const [clientName, setClientName] = useState('');
  const [days, setDays] = useState<RoadbookDay[]>([
    { day: 1, location: '', activities: [], description: '' }
  ]);
  const [destinations, setDestinations] = useState<string[]>([]);
  const [newDestination, setNewDestination] = useState('');
  const [saving, setSaving] = useState(false);
  const [expandedDays, setExpandedDays] = useState<Set<number>>(new Set([0]));
  const [currentDay, setCurrentDay] = useState(0);

  useEffect(() => {
    if (roadbookId) {
      loadRoadbook();
    }
  }, [roadbookId]);

  useEffect(() => {
    if (days.length > 1) {
      const interval = setInterval(() => {
        setCurrentDay((prev) => (prev + 1) % days.length);
      }, 3000);
      return () => clearInterval(interval);
    }
  }, [days.length]);

  const loadRoadbook = async () => {
    if (!db.supabase || !roadbookId) return;
    try {
      const { data, error } = await db.supabase
        .from('travel_roadbooks')
        .select('*')
        .eq('id', roadbookId)
        .single();

      if (error) throw error;
      if (data) {
        setTitle(data.title);
        setSubtitle(data.subtitle || '');
        setClientName(data.client_name);
        setDays(data.days || []);
        setDestinations(data.destinations || []);
      }
    } catch (error) {
      console.error('Error loading roadbook:', error);
    }
  };

  const handleSave = async () => {
    if (!db.supabase || !user?.brand_id) return;
    if (!title.trim() || !clientName.trim()) {
      alert('Vul minimaal een titel en klantnaam in');
      return;
    }

    setSaving(true);
    try {
      const roadbookData = {
        brand_id: user.brand_id,
        title: title.trim(),
        subtitle: subtitle.trim() || null,
        client_name: clientName.trim(),
        days: days.filter(d => d.location.trim()),
        destinations: destinations.filter(d => d.trim()),
        updated_at: new Date().toISOString(),
      };

      if (roadbookId) {
        const { error } = await db.supabase
          .from('travel_roadbooks')
          .update(roadbookData)
          .eq('id', roadbookId);
        if (error) throw error;
      } else {
        const { error } = await db.supabase
          .from('travel_roadbooks')
          .insert({ ...roadbookData, created_at: new Date().toISOString() });
        if (error) throw error;
      }

      onSave();
      onClose();
    } catch (error) {
      console.error('Error saving roadbook:', error);
      alert('Fout bij opslaan');
    } finally {
      setSaving(false);
    }
  };

  const addDay = () => {
    setDays([...days, { 
      day: days.length + 1, 
      location: '', 
      activities: [], 
      description: '' 
    }]);
    setExpandedDays(new Set([...expandedDays, days.length]));
  };

  const removeDay = (index: number) => {
    const newDays = days.filter((_, i) => i !== index);
    newDays.forEach((day, i) => { day.day = i + 1; });
    setDays(newDays);
  };

  const updateDay = (index: number, field: keyof RoadbookDay, value: any) => {
    const newDays = [...days];
    newDays[index] = { ...newDays[index], [field]: value };
    setDays(newDays);
  };

  const addActivity = (dayIndex: number) => {
    const newDays = [...days];
    newDays[dayIndex].activities.push('');
    setDays(newDays);
  };

  const updateActivity = (dayIndex: number, activityIndex: number, value: string) => {
    const newDays = [...days];
    newDays[dayIndex].activities[activityIndex] = value;
    setDays(newDays);
  };

  const removeActivity = (dayIndex: number, activityIndex: number) => {
    const newDays = [...days];
    newDays[dayIndex].activities.splice(activityIndex, 1);
    setDays(newDays);
  };

  const addDestination = () => {
    if (newDestination.trim()) {
      setDestinations([...destinations, newDestination.trim()]);
      setNewDestination('');
    }
  };

  const removeDestination = (index: number) => {
    setDestinations(destinations.filter((_, i) => i !== index));
  };

  const toggleDayExpanded = (index: number) => {
    const newExpanded = new Set(expandedDays);
    if (newExpanded.has(index)) {
      newExpanded.delete(index);
    } else {
      newExpanded.add(index);
    }
    setExpandedDays(newExpanded);
  };

  return (
    <div className="h-screen flex flex-col bg-gray-50">
      {/* Top bar */}
      <div className="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div className="flex items-center gap-4">
          <button
            onClick={onClose}
            className="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors"
          >
            <ArrowLeft className="w-5 h-5" />
            <span className="font-medium">Terug</span>
          </button>
          <div className="h-6 w-px bg-gray-300" />
          <h1 className="text-lg font-semibold text-gray-900">
            {roadbookId ? 'Roadbook Bewerken' : 'Nieuw Roadbook'}
          </h1>
        </div>
        <button
          onClick={handleSave}
          disabled={saving}
          className="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium disabled:opacity-50"
        >
          <Save className="w-4 h-4" />
          {saving ? 'Opslaan...' : 'Opslaan'}
        </button>
      </div>

      {/* Split screen: Editor left, Preview right */}
      <div className="flex-1 flex overflow-hidden">
        {/* Left: Editor */}
        <div className="w-1/2 overflow-y-auto p-6 space-y-6">
          {/* Basic Info */}
          <div className="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 className="text-lg font-semibold text-gray-900">Basis Informatie</h2>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Titel *</label>
              <input
                type="text"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                placeholder="Bijvoorbeeld: Rondreis door Thailand"
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Ondertitel</label>
              <input
                type="text"
                value={subtitle}
                onChange={(e) => setSubtitle(e.target.value)}
                placeholder="Bijvoorbeeld: 14 dagen cultuur en natuur"
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Klantnaam *</label>
              <input
                type="text"
                value={clientName}
                onChange={(e) => setClientName(e.target.value)}
                placeholder="Voor wie is dit roadbook?"
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              />
            </div>
          </div>

          {/* Destinations */}
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Bestemmingen</h2>
            <div className="flex gap-2 mb-3">
              <input
                type="text"
                value={newDestination}
                onChange={(e) => setNewDestination(e.target.value)}
                onKeyPress={(e) => e.key === 'Enter' && addDestination()}
                placeholder="Voeg bestemming toe..."
                className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              />
              <button
                onClick={addDestination}
                className="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
              >
                <Plus className="w-5 h-5" />
              </button>
            </div>
            <div className="flex flex-wrap gap-2">
              {destinations.map((dest, index) => (
                <div
                  key={index}
                  className="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg"
                >
                  <MapPin className="w-4 h-4" />
                  <span className="text-sm font-medium">{dest}</span>
                  <button
                    onClick={() => removeDestination(index)}
                    className="text-emerald-600 hover:text-emerald-800"
                  >
                    <X className="w-4 h-4" />
                  </button>
                </div>
              ))}
            </div>
          </div>

          {/* Days */}
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-lg font-semibold text-gray-900">Dag-tot-dag Programma</h2>
              <button
                onClick={addDay}
                className="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm"
              >
                <Plus className="w-4 h-4" />
                Dag Toevoegen
              </button>
            </div>

            <div className="space-y-3">
              {days.map((day, dayIndex) => (
                <div
                  key={dayIndex}
                  className="border-2 border-gray-200 rounded-xl overflow-hidden"
                >
                  <div
                    className="bg-gradient-to-r from-blue-50 to-purple-50 px-4 py-3 flex items-center justify-between cursor-pointer"
                    onClick={() => toggleDayExpanded(dayIndex)}
                  >
                    <div className="flex items-center gap-3">
                      <GripVertical className="w-5 h-5 text-gray-400" />
                      <Calendar className="w-5 h-5 text-blue-600" />
                      <span className="font-semibold text-gray-900">Dag {day.day}</span>
                      {day.location && <span className="text-sm text-gray-600">- {day.location}</span>}
                    </div>
                    <div className="flex items-center gap-2">
                      <button
                        onClick={(e) => {
                          e.stopPropagation();
                          removeDay(dayIndex);
                        }}
                        className="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                      {expandedDays.has(dayIndex) ? (
                        <ChevronUp className="w-5 h-5 text-gray-400" />
                      ) : (
                        <ChevronDown className="w-5 h-5 text-gray-400" />
                      )}
                    </div>
                  </div>

                  {expandedDays.has(dayIndex) && (
                    <div className="p-4 space-y-4">
                      <div>
                        <label className="block text-xs font-medium text-gray-600 mb-1">Locatie *</label>
                        <input
                          type="text"
                          value={day.location}
                          onChange={(e) => updateDay(dayIndex, 'location', e.target.value)}
                          placeholder="Bijvoorbeeld: Bangkok"
                          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                        />
                      </div>

                      <div>
                        <label className="block text-xs font-medium text-gray-600 mb-1">Datum (optioneel)</label>
                        <input
                          type="text"
                          value={day.date || ''}
                          onChange={(e) => updateDay(dayIndex, 'date', e.target.value)}
                          placeholder="Bijvoorbeeld: 15 maart 2026"
                          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                        />
                      </div>

                      <div className="bg-blue-50 rounded-lg p-3">
                        <div className="flex items-center gap-2 mb-2">
                          <Hotel className="w-4 h-4 text-blue-600" />
                          <span className="text-xs font-medium text-gray-700">Hotel</span>
                        </div>
                        <div className="space-y-2">
                          <input
                            type="text"
                            value={day.hotel?.name || ''}
                            onChange={(e) => updateDay(dayIndex, 'hotel', { ...day.hotel, name: e.target.value })}
                            placeholder="Hotelnaam"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                          />
                          <input
                            type="text"
                            value={day.hotel?.address || ''}
                            onChange={(e) => updateDay(dayIndex, 'hotel', { ...day.hotel, address: e.target.value })}
                            placeholder="Adres"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                          />
                          <input
                            type="number"
                            value={day.hotel?.nights || ''}
                            onChange={(e) => updateDay(dayIndex, 'hotel', { ...day.hotel, nights: parseInt(e.target.value) || 0 })}
                            placeholder="Aantal nachten"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                          />
                        </div>
                      </div>

                      <div>
                        <div className="flex items-center justify-between mb-2">
                          <label className="text-xs font-medium text-gray-600">Activiteiten</label>
                          <button
                            onClick={() => addActivity(dayIndex)}
                            className="text-xs text-emerald-600 hover:text-emerald-700 font-medium"
                          >
                            + Activiteit
                          </button>
                        </div>
                        <div className="space-y-2">
                          {day.activities.map((activity, actIndex) => (
                            <div key={actIndex} className="flex gap-2">
                              <input
                                type="text"
                                value={activity}
                                onChange={(e) => updateActivity(dayIndex, actIndex, e.target.value)}
                                placeholder="Bijvoorbeeld: Bezoek aan Grand Palace"
                                className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                              />
                              <button
                                onClick={() => removeActivity(dayIndex, actIndex)}
                                className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                              >
                                <X className="w-4 h-4" />
                              </button>
                            </div>
                          ))}
                        </div>
                      </div>

                      <div>
                        <label className="block text-xs font-medium text-gray-600 mb-1">Beschrijving</label>
                        <textarea
                          value={day.description || ''}
                          onChange={(e) => updateDay(dayIndex, 'description', e.target.value)}
                          placeholder="Extra informatie over deze dag..."
                          rows={3}
                          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                        />
                      </div>
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Right: Live Preview */}
        <div className="w-1/2 bg-gray-100 border-l border-gray-200 overflow-y-auto p-6">
          <div className="bg-white rounded-xl shadow-lg overflow-hidden">
            <div className="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4 text-white">
              <h1 className="text-2xl font-bold">{title || 'Roadbook Preview'}</h1>
              {subtitle && <p className="text-emerald-100 mt-1">{subtitle}</p>}
              {clientName && <p className="text-emerald-100 mt-2 text-sm">Voor: {clientName}</p>}
            </div>

            <div className="p-6">
              {days.length > 0 && days.some(d => d.location) && (
                <>
                  {/* Route Overview */}
                  <div className="bg-white rounded-xl shadow-md p-4 mb-4 border border-gray-200">
                    <h2 className="text-lg font-bold text-gray-900 mb-3 flex items-center">
                      <Map className="w-5 h-5 mr-2 text-blue-600" />
                      Jouw Route
                    </h2>
                    
                    <div className="relative py-6">
                      <div className="absolute top-1/2 left-0 right-0 h-2 bg-gray-200 rounded-full transform -translate-y-1/2">
                        <div 
                          className="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full transition-all duration-1000"
                          style={{ width: `${((currentDay + 1) / Math.max(days.filter(d => d.location).length, 1)) * 100}%` }}
                        />
                      </div>
                      
                      <div className="relative flex justify-between">
                        {days.filter(d => d.location).map((day, index) => (
                          <div key={index} className="flex flex-col items-center">
                            <div 
                              className={`w-10 h-10 rounded-full flex items-center justify-center z-10 transition-all duration-300 ${
                                index <= currentDay 
                                  ? 'bg-gradient-to-br from-blue-500 to-purple-500 text-white scale-110' 
                                  : 'bg-white border-2 border-gray-300 text-gray-500'
                              }`}
                            >
                              {index === currentDay ? (
                                <Car className="w-5 h-5 animate-bounce" />
                              ) : (
                                <span className="font-bold text-sm">{index + 1}</span>
                              )}
                            </div>
                            <span className={`mt-2 text-xs font-medium ${index <= currentDay ? 'text-gray-900' : 'text-gray-500'}`}>
                              {day.location}
                            </span>
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>

                  {/* Days */}
                  <div className="space-y-3">
                    {days.filter(d => d.location).map((day, index) => (
                      <div 
                        key={index}
                        className={`rounded-lg overflow-hidden border transition-all ${
                          index === currentDay ? 'ring-2 ring-orange-500 border-orange-200' : 'border-gray-200'
                        }`}
                      >
                        <div className="bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-3">
                          <div className="flex items-center justify-between text-white">
                            <div className="flex items-center gap-2">
                              <Calendar className="w-4 h-4" />
                              <span className="font-bold text-sm">Dag {day.day}</span>
                              {day.date && <span className="text-xs opacity-80">• {day.date}</span>}
                            </div>
                            <div className="flex items-center gap-1">
                              <MapPin className="w-4 h-4" />
                              <span className="text-sm font-medium">{day.location}</span>
                            </div>
                          </div>
                        </div>
                        
                        <div className="p-4 bg-white">
                          {day.hotel?.name && (
                            <div className="flex items-start gap-2 mb-3 p-3 bg-blue-50 rounded-lg">
                              <Hotel className="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                              <div>
                                <h4 className="font-semibold text-gray-900 text-sm">{day.hotel.name}</h4>
                                {day.hotel.address && <p className="text-xs text-gray-600">{day.hotel.address}</p>}
                                {day.hotel.nights && <p className="text-xs text-blue-600 mt-1">{day.hotel.nights} nacht(en)</p>}
                              </div>
                            </div>
                          )}
                          
                          {day.activities.length > 0 && day.activities.some(a => a.trim()) && (
                            <div className="space-y-1 mb-3">
                              <h4 className="font-semibold text-gray-900 text-sm mb-1">Activiteiten</h4>
                              {day.activities.filter(a => a.trim()).map((activity, actIndex) => (
                                <div key={actIndex} className="flex items-center gap-2 text-gray-700">
                                  <ChevronRight className="w-3 h-3 text-orange-500" />
                                  <span className="text-sm">{activity}</span>
                                </div>
                              ))}
                            </div>
                          )}
                          
                          {day.description && (
                            <p className="text-gray-600 text-sm">{day.description}</p>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                </>
              )}

              {destinations.length > 0 && (
                <div className="mt-4 bg-white rounded-xl shadow-md p-4 border border-gray-200">
                  <h2 className="text-lg font-bold text-gray-900 mb-3">Bestemmingen</h2>
                  <div className="grid grid-cols-2 gap-2">
                    {destinations.map((dest, index) => (
                      <div key={index} className="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-3 text-center">
                        <MapPin className="w-5 h-5 text-blue-600 mx-auto mb-1" />
                        <span className="font-medium text-gray-900 text-sm">{dest}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

function RoadbookPreview({ roadbook, onClose }: { roadbook: Roadbook; onClose: () => void }) {
  const [currentDay, setCurrentDay] = useState(0);

  useEffect(() => {
    if (roadbook.days.length > 1) {
      const interval = setInterval(() => {
        setCurrentDay((prev) => (prev + 1) % roadbook.days.length);
      }, 3000);
      return () => clearInterval(interval);
    }
  }, [roadbook.days.length]);

  return (
    <div className="fixed inset-0 bg-black/50 z-50 overflow-y-auto">
      <div className="min-h-screen p-6">
        <div className="max-w-5xl mx-auto">
          <div className="bg-white rounded-2xl shadow-2xl overflow-hidden">
            {/* Header */}
            <div className="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-6 text-white">
              <div className="flex items-center justify-between mb-4">
                <h1 className="text-3xl font-bold">{roadbook.title}</h1>
                <button
                  onClick={onClose}
                  className="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors"
                >
                  Sluiten
                </button>
              </div>
              {roadbook.subtitle && <p className="text-emerald-100">{roadbook.subtitle}</p>}
              <p className="text-emerald-100 mt-2">Voor: {roadbook.client_name}</p>
            </div>

            <div className="p-8">
              {/* Route Overview with Car Animation */}
              <div className="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
                <h2 className="text-xl font-bold text-gray-900 mb-4 flex items-center">
                  <Map className="w-6 h-6 mr-2 text-blue-600" />
                  Jouw Route
                </h2>
                
                {/* Animated Route Line */}
                <div className="relative py-8">
                  <div className="absolute top-1/2 left-0 right-0 h-2 bg-gray-200 rounded-full transform -translate-y-1/2">
                    <div 
                      className="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full transition-all duration-1000"
                      style={{ width: `${((currentDay + 1) / Math.max(roadbook.days.length, 1)) * 100}%` }}
                    />
                  </div>
                  
                  {/* Route Points */}
                  <div className="relative flex justify-between">
                    {roadbook.days.map((day, index) => (
                      <div key={index} className="flex flex-col items-center">
                        <div 
                          className={`w-12 h-12 rounded-full flex items-center justify-center z-10 transition-all duration-300 ${
                            index <= currentDay 
                              ? 'bg-gradient-to-br from-blue-500 to-purple-500 text-white scale-110' 
                              : 'bg-white border-2 border-gray-300 text-gray-500'
                          }`}
                        >
                          {index === currentDay ? (
                            <Car className="w-6 h-6 animate-bounce" />
                          ) : (
                            <span className="font-bold">{index + 1}</span>
                          )}
                        </div>
                        <span className={`mt-2 text-sm font-medium ${index <= currentDay ? 'text-gray-900' : 'text-gray-500'}`}>
                          {day.location}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>

              {/* Day by Day Itinerary */}
              <div className="space-y-4">
                {roadbook.days.map((day, index) => (
                  <div 
                    key={index}
                    className={`bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 border ${
                      index === currentDay ? 'ring-2 ring-orange-500 scale-[1.02] border-orange-200' : 'border-gray-200'
                    }`}
                  >
                    <div className="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                      <div className="flex items-center justify-between">
                        <div className="flex items-center space-x-3">
                          <div className="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <Calendar className="w-5 h-5 text-white" />
                          </div>
                          <div>
                            <h3 className="text-lg font-bold text-white">Dag {day.day}</h3>
                            {day.date && <p className="text-white/80 text-sm">{day.date}</p>}
                          </div>
                        </div>
                        <div className="flex items-center space-x-2 text-white">
                          <MapPin className="w-5 h-5" />
                          <span className="font-medium">{day.location}</span>
                        </div>
                      </div>
                    </div>
                    
                    <div className="p-6">
                      {/* Hotel Info */}
                      {day.hotel && (
                        <div className="flex items-start space-x-3 mb-4 p-4 bg-blue-50 rounded-lg">
                          <Hotel className="w-6 h-6 text-blue-600 flex-shrink-0 mt-1" />
                          <div>
                            <h4 className="font-semibold text-gray-900">{day.hotel.name}</h4>
                            {day.hotel.address && <p className="text-sm text-gray-600">{day.hotel.address}</p>}
                            {day.hotel.nights && <p className="text-sm text-blue-600">{day.hotel.nights} nacht(en)</p>}
                          </div>
                        </div>
                      )}
                      
                      {/* Activities */}
                      {day.activities && day.activities.length > 0 && (
                        <div className="space-y-2 mb-4">
                          <h4 className="font-semibold text-gray-900 mb-2">Activiteiten</h4>
                          {day.activities.map((activity, actIndex) => (
                            <div key={actIndex} className="flex items-center space-x-2 text-gray-700">
                              <ChevronRight className="w-4 h-4 text-orange-500" />
                              <span>{activity}</span>
                            </div>
                          ))}
                        </div>
                      )}
                      
                      {/* Description */}
                      {day.description && (
                        <p className="text-gray-600">{day.description}</p>
                      )}
                    </div>
                  </div>
                ))}
              </div>

              {/* Destinations */}
              {roadbook.destinations.length > 0 && (
                <div className="mt-6 bg-white rounded-2xl shadow-xl p-6 border border-gray-200">
                  <h2 className="text-xl font-bold text-gray-900 mb-4">Bestemmingen</h2>
                  <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                    {roadbook.destinations.map((dest, index) => (
                      <div key={index} className="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-4 text-center">
                        <MapPin className="w-6 h-6 text-blue-600 mx-auto mb-2" />
                        <span className="font-medium text-gray-900">{dest}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
