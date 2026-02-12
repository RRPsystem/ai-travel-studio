import { useState, useEffect } from 'react';
import { ArrowLeft, Save, Plus, X, Calendar, MapPin, Hotel, Car, GripVertical, ChevronDown, ChevronUp } from 'lucide-react';
import { useAuth } from '../../contexts/AuthContext';
import { db } from '../../lib/supabase';

interface RoadDay {
  day: number;
  date?: string;
  location: string;
  distance?: string;
  duration?: string;
  hotel?: {
    name: string;
    address?: string;
    nights?: number;
  };
  highlights: string[];
  description?: string;
}

interface AutoRondreis {
  id?: string;
  brand_id?: string;
  client_name: string;
  title: string;
  subtitle?: string;
  days: RoadDay[];
  total_days: number;
  total_distance?: string;
  created_at?: string;
  updated_at?: string;
}

interface Props {
  roadbook?: AutoRondreis;
  onBack: () => void;
  onSave?: (roadbook: AutoRondreis) => void;
}

export function AutoRondreisEditor({ roadbook, onBack, onSave }: Props) {
  const { user } = useAuth();
  const [title, setTitle] = useState(roadbook?.title || '');
  const [subtitle, setSubtitle] = useState(roadbook?.subtitle || '');
  const [clientName, setClientName] = useState(roadbook?.client_name || '');
  const [totalDistance, setTotalDistance] = useState(roadbook?.total_distance || '');
  const [days, setDays] = useState<RoadDay[]>(roadbook?.days || [
    { day: 1, location: '', distance: '', duration: '', highlights: [], description: '' }
  ]);
  const [saving, setSaving] = useState(false);
  const [expandedDays, setExpandedDays] = useState<Set<number>>(new Set([0]));
  const [currentDay, setCurrentDay] = useState(0);

  // Animate car through route
  useEffect(() => {
    if (days.length > 1) {
      const interval = setInterval(() => {
        setCurrentDay((prev) => (prev + 1) % days.length);
      }, 3000);
      return () => clearInterval(interval);
    }
  }, [days.length]);

  const handleSave = async () => {
    if (!user?.brand_id) return;
    if (!title.trim() || !clientName.trim()) {
      alert('Vul minimaal een titel en klantnaam in');
      return;
    }

    setSaving(true);
    try {
      const roadbookData: AutoRondreis = {
        id: roadbook?.id,
        brand_id: user.brand_id,
        title: title.trim(),
        subtitle: subtitle.trim() || undefined,
        client_name: clientName.trim(),
        days: days.filter(d => d.location.trim()),
        total_days: days.length,
        total_distance: totalDistance || undefined,
        updated_at: new Date().toISOString(),
      };

      if (onSave) {
        await onSave(roadbookData);
      }
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
      distance: '',
      duration: '',
      highlights: [], 
      description: '' 
    }]);
    setExpandedDays(new Set([...expandedDays, days.length]));
  };

  const removeDay = (index: number) => {
    const newDays = days.filter((_, i) => i !== index);
    newDays.forEach((day, i) => { day.day = i + 1; });
    setDays(newDays);
  };

  const updateDay = (index: number, field: keyof RoadDay, value: any) => {
    const newDays = [...days];
    newDays[index] = { ...newDays[index], [field]: value };
    setDays(newDays);
  };

  const addHighlight = (dayIndex: number) => {
    const newDays = [...days];
    newDays[dayIndex].highlights.push('');
    setDays(newDays);
  };

  const updateHighlight = (dayIndex: number, highlightIndex: number, value: string) => {
    const newDays = [...days];
    newDays[dayIndex].highlights[highlightIndex] = value;
    setDays(newDays);
  };

  const removeHighlight = (dayIndex: number, highlightIndex: number) => {
    const newDays = [...days];
    newDays[dayIndex].highlights.splice(highlightIndex, 1);
    setDays(newDays);
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
            onClick={onBack}
            className="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors"
          >
            <ArrowLeft className="w-5 h-5" />
            <span className="font-medium">Terug</span>
          </button>
          <div className="h-6 w-px bg-gray-300" />
          <h1 className="text-lg font-semibold text-gray-900">Auto Rondreis</h1>
        </div>
        <button
          onClick={handleSave}
          disabled={saving}
          className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50"
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
            <h2 className="text-lg font-semibold text-gray-900">Reis Informatie</h2>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Titel *</label>
              <input
                type="text"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                placeholder="Bijvoorbeeld: Rondreis Zuid-Afrika"
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Ondertitel</label>
              <input
                type="text"
                value={subtitle}
                onChange={(e) => setSubtitle(e.target.value)}
                placeholder="Bijvoorbeeld: 14 dagen langs de Garden Route"
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Klantnaam *</label>
              <input
                type="text"
                value={clientName}
                onChange={(e) => setClientName(e.target.value)}
                placeholder="Voor wie is deze rondreis?"
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Totale Afstand</label>
              <input
                type="text"
                value={totalDistance}
                onChange={(e) => setTotalDistance(e.target.value)}
                placeholder="Bijvoorbeeld: 2.500 km"
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          {/* Days */}
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-lg font-semibold text-gray-900">Route Planning</h2>
              <button
                onClick={addDay}
                className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
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
                    className="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 flex items-center justify-between cursor-pointer"
                    onClick={() => toggleDayExpanded(dayIndex)}
                  >
                    <div className="flex items-center gap-3">
                      <GripVertical className="w-5 h-5 text-gray-400" />
                      <Car className="w-5 h-5 text-blue-600" />
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
                        <X className="w-4 h-4" />
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
                      <div className="grid grid-cols-2 gap-3">
                        <div>
                          <label className="block text-xs font-medium text-gray-600 mb-1">Locatie *</label>
                          <input
                            type="text"
                            value={day.location}
                            onChange={(e) => updateDay(dayIndex, 'location', e.target.value)}
                            placeholder="Bijvoorbeeld: Kaapstad"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-medium text-gray-600 mb-1">Datum</label>
                          <input
                            type="text"
                            value={day.date || ''}
                            onChange={(e) => updateDay(dayIndex, 'date', e.target.value)}
                            placeholder="15 maart 2026"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                          />
                        </div>
                      </div>

                      <div className="grid grid-cols-2 gap-3">
                        <div>
                          <label className="block text-xs font-medium text-gray-600 mb-1">Afstand</label>
                          <input
                            type="text"
                            value={day.distance || ''}
                            onChange={(e) => updateDay(dayIndex, 'distance', e.target.value)}
                            placeholder="250 km"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-medium text-gray-600 mb-1">Rijtijd</label>
                          <input
                            type="text"
                            value={day.duration || ''}
                            onChange={(e) => updateDay(dayIndex, 'duration', e.target.value)}
                            placeholder="3,5 uur"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                          />
                        </div>
                      </div>

                      <div className="bg-blue-50 rounded-lg p-3">
                        <div className="flex items-center gap-2 mb-2">
                          <Hotel className="w-4 h-4 text-blue-600" />
                          <span className="text-xs font-medium text-gray-700">Accommodatie</span>
                        </div>
                        <div className="space-y-2">
                          <input
                            type="text"
                            value={day.hotel?.name || ''}
                            onChange={(e) => updateDay(dayIndex, 'hotel', { ...day.hotel, name: e.target.value })}
                            placeholder="Hotel naam"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                          />
                          <input
                            type="text"
                            value={day.hotel?.address || ''}
                            onChange={(e) => updateDay(dayIndex, 'hotel', { ...day.hotel, address: e.target.value })}
                            placeholder="Locatie"
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
                          <label className="text-xs font-medium text-gray-600">Highlights</label>
                          <button
                            onClick={() => addHighlight(dayIndex)}
                            className="text-xs text-blue-600 hover:text-blue-700 font-medium"
                          >
                            + Highlight
                          </button>
                        </div>
                        <div className="space-y-2">
                          {day.highlights.map((highlight, highlightIndex) => (
                            <div key={highlightIndex} className="flex gap-2">
                              <input
                                type="text"
                                value={highlight}
                                onChange={(e) => updateHighlight(dayIndex, highlightIndex, e.target.value)}
                                placeholder="Bijvoorbeeld: Tafelberg beklimmen"
                                className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                              />
                              <button
                                onClick={() => removeHighlight(dayIndex, highlightIndex)}
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
                          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        />
                      </div>
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Right: Live Preview with animated car */}
        <div className="w-1/2 bg-gradient-to-br from-blue-50 to-indigo-50 border-l border-gray-200 overflow-y-auto p-6">
          <div className="bg-white rounded-xl shadow-lg overflow-hidden">
            {/* Header */}
            <div className="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-6 text-white">
              <h1 className="text-2xl font-bold">{title || 'Auto Rondreis Preview'}</h1>
              {subtitle && <p className="text-blue-100 mt-1">{subtitle}</p>}
              {clientName && <p className="text-blue-100 mt-2 text-sm">Voor: {clientName}</p>}
              {totalDistance && (
                <div className="mt-4 flex items-center gap-2 text-sm">
                  <Car className="w-4 h-4" />
                  <span>Totaal: {totalDistance}</span>
                </div>
              )}
            </div>

            <div className="p-6">
              {days.length > 0 && days.some(d => d.location) && (
                <>
                  {/* Animated Route Timeline */}
                  <div className="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6">
                    <h2 className="text-lg font-bold text-gray-900 mb-4 flex items-center">
                      <Car className="w-5 h-5 mr-2 text-blue-600" />
                      Jouw Route
                    </h2>
                    
                    <div className="relative py-8">
                      {/* Road line */}
                      <div className="absolute top-1/2 left-0 right-0 h-3 bg-gray-300 rounded-full transform -translate-y-1/2">
                        {/* Yellow center line */}
                        <div className="absolute top-1/2 left-0 right-0 h-0.5 bg-yellow-400 transform -translate-y-1/2" />
                        {/* Progress */}
                        <div 
                          className="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-1000"
                          style={{ width: `${((currentDay + 1) / Math.max(days.filter(d => d.location).length, 1)) * 100}%` }}
                        />
                      </div>
                      
                      {/* Route Points */}
                      <div className="relative flex justify-between">
                        {days.filter(d => d.location).map((day, index) => (
                          <div key={index} className="flex flex-col items-center">
                            <div 
                              className={`w-12 h-12 rounded-full flex items-center justify-center z-10 transition-all duration-300 ${
                                index <= currentDay 
                                  ? 'bg-gradient-to-br from-blue-500 to-indigo-500 text-white scale-110 shadow-lg' 
                                  : 'bg-white border-2 border-gray-300 text-gray-500'
                              }`}
                            >
                              {index === currentDay ? (
                                <Car className="w-6 h-6 animate-bounce" />
                              ) : (
                                <span className="font-bold text-sm">{index + 1}</span>
                              )}
                            </div>
                            <span className={`mt-2 text-xs font-medium text-center ${index <= currentDay ? 'text-gray-900' : 'text-gray-500'}`}>
                              {day.location}
                            </span>
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>

                  {/* Days */}
                  <div className="space-y-4">
                    {days.filter(d => d.location).map((day, index) => (
                      <div 
                        key={index}
                        className={`rounded-xl overflow-hidden border-2 transition-all ${
                          index === currentDay ? 'ring-2 ring-blue-500 border-blue-200 shadow-lg' : 'border-gray-200'
                        }`}
                      >
                        <div className="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3">
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
                          {(day.distance || day.duration) && (
                            <div className="flex items-center gap-4 mt-2 text-xs text-blue-100">
                              {day.distance && <span>📍 {day.distance}</span>}
                              {day.duration && <span>⏱️ {day.duration}</span>}
                            </div>
                          )}
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
                          
                          {day.highlights.length > 0 && day.highlights.some(h => h.trim()) && (
                            <div className="space-y-1 mb-3">
                              <h4 className="font-semibold text-gray-900 text-sm mb-1">Highlights</h4>
                              {day.highlights.filter(h => h.trim()).map((highlight, hIndex) => (
                                <div key={hIndex} className="flex items-center gap-2 text-gray-700">
                                  <span className="text-blue-500">✓</span>
                                  <span className="text-sm">{highlight}</span>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
