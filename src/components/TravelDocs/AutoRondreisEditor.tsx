import { useState, useEffect } from 'react';
import {
  ArrowLeft, Save, MapPin, Plus, GripVertical, X,
  Car, Hotel, ChevronDown, ChevronUp, Image, Video, Calendar
} from 'lucide-react';
import { SlidingMediaSelector } from '../shared/SlidingMediaSelector';

interface RoadDay {
  id: string;
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
  intro_text?: string;
  hero_images?: string[];
  hero_video_url?: string;
  departure_date?: string;
  days: RoadDay[];
  destinations?: { name: string; lat: number; lng: number; order: number }[];
  created_at?: string;
  updated_at?: string;
}

interface Props {
  roadbook?: AutoRondreis;
  onBack: () => void;
  onSave?: (roadbook: AutoRondreis) => void;
}

// Countdown Timer Component
function CountdownTimer({ targetDate }: { targetDate: string }) {
  const [timeLeft, setTimeLeft] = useState({ days: 0, hours: 0, minutes: 0, seconds: 0 });

  useEffect(() => {
    if (!targetDate) return;

    const calculateTimeLeft = () => {
      const difference = new Date(targetDate).getTime() - new Date().getTime();
      
      if (difference > 0) {
        setTimeLeft({
          days: Math.floor(difference / (1000 * 60 * 60 * 24)),
          hours: Math.floor((difference / (1000 * 60 * 60)) % 24),
          minutes: Math.floor((difference / 1000 / 60) % 60),
          seconds: Math.floor((difference / 1000) % 60)
        });
      }
    };

    calculateTimeLeft();
    const timer = setInterval(calculateTimeLeft, 1000);
    return () => clearInterval(timer);
  }, [targetDate]);

  if (!targetDate) return null;

  return (
    <div className="space-y-2">
      <div className="text-sm text-white/60">Vertrek over</div>
      <div className="grid grid-cols-4 gap-2">
        <div className="bg-white/10 rounded-lg p-2 text-center">
          <div className="text-2xl font-bold text-white">{timeLeft.days}</div>
          <div className="text-xs text-white/60">dagen</div>
        </div>
        <div className="bg-white/10 rounded-lg p-2 text-center">
          <div className="text-2xl font-bold text-white">{timeLeft.hours}</div>
          <div className="text-xs text-white/60">uur</div>
        </div>
        <div className="bg-white/10 rounded-lg p-2 text-center">
          <div className="text-2xl font-bold text-white">{timeLeft.minutes}</div>
          <div className="text-xs text-white/60">min</div>
        </div>
        <div className="bg-white/10 rounded-lg p-2 text-center">
          <div className="text-2xl font-bold text-white">{timeLeft.seconds}</div>
          <div className="text-xs text-white/60">sec</div>
        </div>
      </div>
    </div>
  );
}

export function AutoRondreisEditor({ roadbook, onBack, onSave }: Props) {
  const [title, setTitle] = useState(roadbook?.title || '');
  const [subtitle, setSubtitle] = useState(roadbook?.subtitle || '');
  const [introText, setIntroText] = useState(roadbook?.intro_text || '');
  const [heroImages, setHeroImages] = useState<string[]>(roadbook?.hero_images || []);
  const [heroVideo, setHeroVideo] = useState(roadbook?.hero_video_url || '');
  const [heroSlideIndex, setHeroSlideIndex] = useState(0);
  const [clientName, setClientName] = useState(roadbook?.client_name || '');
  const [departureDate, setDepartureDate] = useState(roadbook?.departure_date || '');
  const [destinations, setDestinations] = useState(roadbook?.destinations || []);
  const [days, setDays] = useState<RoadDay[]>(roadbook?.days || []);
  const [saving, setSaving] = useState(false);
  const [expandedDays, setExpandedDays] = useState<Set<string>>(new Set());
  const [showMediaSelector, setShowMediaSelector] = useState(false);
  const [mediaSelectorMode, setMediaSelectorMode] = useState<'photo' | 'video'>('photo');
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
    if (!title.trim() || !clientName.trim()) {
      alert('Vul minimaal een titel en klantnaam in');
      return;
    }

    setSaving(true);
    try {
      const roadbookData: AutoRondreis = {
        id: roadbook?.id,
        brand_id: roadbook?.brand_id,
        title: title.trim(),
        subtitle: subtitle.trim() || undefined,
        intro_text: introText.trim() || undefined,
        hero_images: heroImages.length > 0 ? heroImages : undefined,
        hero_video_url: heroVideo || undefined,
        client_name: clientName.trim(),
        departure_date: departureDate || undefined,
        days: days.filter(d => d.location.trim()),
        destinations,
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
    const newDay: RoadDay = {
      id: crypto.randomUUID(),
      day: days.length + 1,
      location: '',
      distance: '',
      duration: '',
      highlights: [],
      description: ''
    };
    setDays([...days, newDay]);
    setExpandedDays(new Set([...expandedDays, newDay.id]));
  };

  const removeDay = (id: string) => {
    const newDays = days.filter(d => d.id !== id);
    newDays.forEach((day, i) => { day.day = i + 1; });
    setDays(newDays);
  };

  const updateDay = (id: string, field: keyof RoadDay, value: any) => {
    setDays(days.map(d => d.id === id ? { ...d, [field]: value } : d));
  };

  const toggleDayExpanded = (id: string) => {
    const newExpanded = new Set(expandedDays);
    if (newExpanded.has(id)) {
      newExpanded.delete(id);
    } else {
      newExpanded.add(id);
    }
    setExpandedDays(newExpanded);
  };

  return (
    <div className="h-screen flex flex-col bg-gray-50">
      {/* Top bar */}
      <div className="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0">
        <div className="flex items-center gap-4">
          <button onClick={onBack} className="flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <ArrowLeft className="w-5 h-5" />
            <span className="font-medium">Terug</span>
          </button>
          <div className="h-6 w-px bg-gray-300" />
          <h1 className="text-lg font-semibold text-gray-900">Auto Rondreis Editor</h1>
        </div>
        <button
          onClick={handleSave}
          disabled={saving}
          className="flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 disabled:opacity-50 font-medium"
        >
          <Save className="w-4 h-4" />
          {saving ? 'Opslaan...' : 'Opslaan'}
        </button>
      </div>

      {/* Split screen */}
      <div className="flex-1 flex overflow-hidden">
        {/* Left: Editor */}
        <div className="w-1/2 overflow-y-auto">
          {/* Settings Panel */}
          <div className="bg-white border-b border-gray-200 p-6 space-y-4">
            <h2 className="text-lg font-semibold text-gray-900">Instellingen</h2>
            
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Klantnaam *</label>
                <input
                  type="text"
                  value={clientName}
                  onChange={(e) => setClientName(e.target.value)}
                  placeholder="Voor wie is deze rondreis?"
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Vertrekdatum</label>
                <input
                  type="date"
                  value={departureDate}
                  onChange={(e) => setDepartureDate(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                />
              </div>
            </div>
          </div>

          {/* Days Editor */}
          <div className="p-6 space-y-4">
            <div className="flex items-center justify-between">
              <h2 className="text-lg font-semibold text-gray-900">Route Planning</h2>
              <button
                onClick={addDay}
                className="flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm"
              >
                <Plus className="w-4 h-4" />
                Dag Toevoegen
              </button>
            </div>

            <div className="space-y-3">
              {days.map((day) => (
                <div key={day.id} className="border-2 border-gray-200 rounded-xl overflow-hidden">
                  <div
                    className="bg-gradient-to-r from-orange-50 to-amber-50 px-4 py-3 flex items-center justify-between cursor-pointer"
                    onClick={() => toggleDayExpanded(day.id)}
                  >
                    <div className="flex items-center gap-3">
                      <GripVertical className="w-5 h-5 text-gray-400" />
                      <Car className="w-5 h-5 text-orange-600" />
                      <span className="font-semibold text-gray-900">Dag {day.day}</span>
                      {day.location && <span className="text-sm text-gray-600">- {day.location}</span>}
                    </div>
                    <div className="flex items-center gap-2">
                      <button
                        onClick={(e) => { e.stopPropagation(); removeDay(day.id); }}
                        className="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"
                      >
                        <X className="w-4 h-4" />
                      </button>
                      {expandedDays.has(day.id) ? <ChevronUp className="w-5 h-5 text-gray-400" /> : <ChevronDown className="w-5 h-5 text-gray-400" />}
                    </div>
                  </div>

                  {expandedDays.has(day.id) && (
                    <div className="p-4 bg-white space-y-4">
                      <div className="grid grid-cols-2 gap-3">
                        <div>
                          <label className="block text-xs font-medium text-gray-600 mb-1">Locatie *</label>
                          <input
                            type="text"
                            value={day.location}
                            onChange={(e) => updateDay(day.id, 'location', e.target.value)}
                            placeholder="Bijvoorbeeld: Kaapstad"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-medium text-gray-600 mb-1">Datum</label>
                          <input
                            type="text"
                            value={day.date || ''}
                            onChange={(e) => updateDay(day.id, 'date', e.target.value)}
                            placeholder="15 maart 2026"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                          />
                        </div>
                      </div>

                      <div className="grid grid-cols-2 gap-3">
                        <div>
                          <label className="block text-xs font-medium text-gray-600 mb-1">Afstand</label>
                          <input
                            type="text"
                            value={day.distance || ''}
                            onChange={(e) => updateDay(day.id, 'distance', e.target.value)}
                            placeholder="250 km"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                          />
                        </div>
                        <div>
                          <label className="block text-xs font-medium text-gray-600 mb-1">Rijtijd</label>
                          <input
                            type="text"
                            value={day.duration || ''}
                            onChange={(e) => updateDay(day.id, 'duration', e.target.value)}
                            placeholder="3,5 uur"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                          />
                        </div>
                      </div>

                      <div className="bg-orange-50 rounded-lg p-3">
                        <div className="flex items-center gap-2 mb-2">
                          <Hotel className="w-4 h-4 text-orange-600" />
                          <span className="text-xs font-medium text-gray-700">Accommodatie</span>
                        </div>
                        <div className="space-y-2">
                          <input
                            type="text"
                            value={day.hotel?.name || ''}
                            onChange={(e) => updateDay(day.id, 'hotel', { ...day.hotel, name: e.target.value })}
                            placeholder="Hotel naam"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                          />
                          <input
                            type="text"
                            value={day.hotel?.address || ''}
                            onChange={(e) => updateDay(day.id, 'hotel', { ...day.hotel, address: e.target.value })}
                            placeholder="Locatie"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                          />
                          <input
                            type="number"
                            value={day.hotel?.nights || ''}
                            onChange={(e) => updateDay(day.id, 'hotel', { ...day.hotel, nights: parseInt(e.target.value) || 0 })}
                            placeholder="Aantal nachten"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                          />
                        </div>
                      </div>

                      <div>
                        <div className="flex items-center justify-between mb-2">
                          <label className="text-xs font-medium text-gray-600">Highlights</label>
                          <button
                            onClick={() => updateDay(day.id, 'highlights', [...day.highlights, ''])}
                            className="text-xs text-orange-600 hover:text-orange-700 font-medium"
                          >
                            + Highlight
                          </button>
                        </div>
                        <div className="space-y-2">
                          {day.highlights.map((highlight, hIndex) => (
                            <div key={hIndex} className="flex gap-2">
                              <input
                                type="text"
                                value={highlight}
                                onChange={(e) => {
                                  const newHighlights = [...day.highlights];
                                  newHighlights[hIndex] = e.target.value;
                                  updateDay(day.id, 'highlights', newHighlights);
                                }}
                                placeholder="Bijvoorbeeld: Tafelberg beklimmen"
                                className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                              />
                              <button
                                onClick={() => updateDay(day.id, 'highlights', day.highlights.filter((_, i) => i !== hIndex))}
                                className="p-2 text-red-600 hover:bg-red-50 rounded-lg"
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
                          onChange={(e) => updateDay(day.id, 'description', e.target.value)}
                          placeholder="Extra informatie over deze dag..."
                          rows={3}
                          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                        />
                      </div>
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Right: Live Preview (EXACT SAME AS OFFERTE) */}
        <div className="w-1/2 bg-white overflow-y-auto">
          {/* Hero Section - EXACT SAME AS OFFERTE */}
          <div className="relative bg-slate-900" style={{ minHeight: '70vh' }}>
            {/* Background media */}
            {heroVideo ? (
              <video
                key={heroVideo}
                src={heroVideo}
                autoPlay
                muted
                loop
                playsInline
                className="absolute inset-0 w-full h-full object-cover"
              />
            ) : heroImages.length > 0 ? (
              <>
                {heroImages.map((img, i) => (
                  <img
                    key={img}
                    src={img}
                    alt=""
                    className="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
                    style={{ opacity: i === heroSlideIndex ? 1 : 0 }}
                  />
                ))}
                {heroImages.length > 1 && (
                  <>
                    <button
                      onClick={() => setHeroSlideIndex((heroSlideIndex - 1 + heroImages.length) % heroImages.length)}
                      className="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white rounded-full flex items-center justify-center"
                    >
                      <ArrowLeft size={18} />
                    </button>
                    <button
                      onClick={() => setHeroSlideIndex((heroSlideIndex + 1) % heroImages.length)}
                      className="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white rounded-full flex items-center justify-center"
                    >
                      <ArrowLeft size={18} className="rotate-180" />
                    </button>
                    <div className="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5">
                      {heroImages.map((_, i) => (
                        <button
                          key={i}
                          onClick={() => setHeroSlideIndex(i)}
                          className={`w-2 h-2 rounded-full transition-all ${i === heroSlideIndex ? 'bg-white w-4' : 'bg-white/40 hover:bg-white/60'}`}
                        />
                      ))}
                    </div>
                  </>
                )}
              </>
            ) : (
              <div className="absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-700 to-slate-900" />
            )}

            {/* Dark overlay */}
            <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent pointer-events-none" />

            {/* Upload controls */}
            <div className="absolute top-4 right-4 z-20 flex items-center gap-2">
              <button onClick={() => { setMediaSelectorMode('photo'); setShowMediaSelector(true); }} className="px-3 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg text-xs font-medium flex items-center gap-2">
                <Image size={14} />
                {heroImages.length > 0 ? 'Foto +' : 'Foto'}
              </button>
              <button onClick={() => { setMediaSelectorMode('video'); setShowMediaSelector(true); }} className="px-3 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg text-xs font-medium flex items-center gap-2">
                <Video size={14} />
                Video
              </button>
              {heroImages.length > 0 && !heroVideo && (
                <button onClick={() => { setHeroImages(heroImages.filter((_, i) => i !== heroSlideIndex)); setHeroSlideIndex(0); }} className="px-3 py-2 bg-red-500/60 backdrop-blur-sm hover:bg-red-500/80 text-white rounded-lg text-xs font-medium flex items-center gap-2">
                  <X size={14} />
                  Verwijder
                </button>
              )}
              {heroVideo && (
                <button onClick={() => setHeroVideo('')} className="px-3 py-2 bg-red-500/60 backdrop-blur-sm hover:bg-red-500/80 text-white rounded-lg text-xs font-medium flex items-center gap-2">
                  <X size={14} />
                  Verwijder video
                </button>
              )}
            </div>

            {/* Content overlay */}
            <div className="relative z-10 flex h-full" style={{ minHeight: '70vh' }}>
              <div className="w-1/2 p-10 flex flex-col justify-center">
                <input
                  type="text"
                  value={subtitle}
                  onChange={e => setSubtitle(e.target.value)}
                  placeholder="Subtitel bijv. '14 dagen door Zuidoost-Azië'"
                  className="text-sm font-medium text-orange-400 bg-transparent border-none outline-none placeholder:text-white/30 mb-3 tracking-wider uppercase"
                />
                <input
                  type="text"
                  value={title}
                  onChange={e => setTitle(e.target.value)}
                  placeholder="Reistitel..."
                  className="text-4xl font-bold text-white bg-transparent border-none outline-none placeholder:text-white/20 mb-4 leading-tight"
                />
                <textarea
                  value={introText}
                  onChange={e => setIntroText(e.target.value)}
                  placeholder="Schrijf een inspirerende introductietekst voor je klant..."
                  rows={4}
                  className="text-base text-white/80 bg-transparent border-none outline-none placeholder:text-white/20 resize-none leading-relaxed mb-8"
                />

                {/* Route map placeholder */}
                <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                  <div className="flex items-center gap-2 mb-3">
                    <MapPin size={16} className="text-orange-400" />
                    <span className="text-sm font-medium text-white/90">Routekaart</span>
                  </div>
                  {destinations.length > 0 ? (
                    <div className="flex flex-wrap gap-2">
                      {destinations.map((dest, i) => (
                        <div key={i} className="flex items-center gap-2">
                          <span className="w-6 h-6 rounded-full bg-orange-500 text-white text-xs flex items-center justify-center font-bold">{i + 1}</span>
                          <span className="text-sm text-white/80">{dest.name}</span>
                          {i < destinations.length - 1 && <span className="text-white/30">→</span>}
                        </div>
                      ))}
                    </div>
                  ) : (
                    <div className="text-sm text-white/40">
                      Bestemmingen worden automatisch toegevoegd op basis van je route
                    </div>
                  )}
                  <button
                    onClick={() => {
                      const name = prompt('Bestemming naam:');
                      if (name) {
                        setDestinations([...destinations, { name, lat: 0, lng: 0, order: destinations.length }]);
                      }
                    }}
                    className="mt-3 text-xs text-orange-400 hover:text-orange-300 flex items-center gap-1"
                  >
                    <Plus size={12} />
                    Bestemming toevoegen
                  </button>
                </div>
              </div>

              {/* Right side - COUNTDOWN TIMER instead of price */}
              <div className="w-1/2 p-10 flex items-end justify-end">
                <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/10 min-w-[280px]">
                  {departureDate ? (
                    <CountdownTimer targetDate={departureDate} />
                  ) : (
                    <div className="text-center py-4">
                      <Calendar className="w-8 h-8 text-white/40 mx-auto mb-2" />
                      <div className="text-sm text-white/60">Stel een vertrekdatum in</div>
                    </div>
                  )}
                  <div className="border-t border-white/10 pt-3 mt-4 space-y-1">
                    <div className="flex justify-between text-sm">
                      <span className="text-white/60">Dagen</span>
                      <span className="text-white/80">{days.length}</span>
                    </div>
                    {departureDate && (
                      <div className="flex justify-between text-sm">
                        <span className="text-white/60">Vertrek</span>
                        <span className="text-white/80">{new Date(departureDate).toLocaleDateString('nl-NL')}</span>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Animated Route Timeline */}
          {days.length > 0 && days.some(d => d.location) && (
            <div className="bg-gradient-to-r from-orange-50 to-amber-50 p-6">
              <h2 className="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <Car className="w-5 h-5 mr-2 text-orange-600" />
                Jouw Route
              </h2>
              
              <div className="relative py-8">
                {/* Road line */}
                <div className="absolute top-1/2 left-0 right-0 h-3 bg-gray-300 rounded-full transform -translate-y-1/2">
                  <div className="absolute top-1/2 left-0 right-0 h-0.5 bg-yellow-400 transform -translate-y-1/2" />
                  <div 
                    className="h-full bg-gradient-to-r from-orange-500 to-amber-500 rounded-full transition-all duration-1000"
                    style={{ width: `${((currentDay + 1) / Math.max(days.filter(d => d.location).length, 1)) * 100}%` }}
                  />
                </div>
                
                {/* Route Points */}
                <div className="relative flex justify-between">
                  {days.filter(d => d.location).map((day, index) => (
                    <div key={day.id} className="flex flex-col items-center">
                      <div 
                        className={`w-12 h-12 rounded-full flex items-center justify-center z-10 transition-all duration-300 ${
                          index <= currentDay 
                            ? 'bg-gradient-to-br from-orange-500 to-amber-500 text-white scale-110 shadow-lg' 
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
          )}

          {/* Days Preview */}
          <div className="p-6 space-y-4">
            {days.filter(d => d.location).map((day, index) => (
              <div 
                key={day.id}
                className={`rounded-xl overflow-hidden border-2 transition-all ${
                  index === currentDay ? 'ring-2 ring-orange-500 border-orange-200 shadow-lg' : 'border-gray-200'
                }`}
              >
                <div className="bg-gradient-to-r from-orange-600 to-amber-600 px-4 py-3">
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
                    <div className="flex items-center gap-4 mt-2 text-xs text-orange-100">
                      {day.distance && <span>📍 {day.distance}</span>}
                      {day.duration && <span>⏱️ {day.duration}</span>}
                    </div>
                  )}
                </div>
                
                <div className="p-4 bg-white">
                  {day.hotel?.name && (
                    <div className="flex items-start gap-2 mb-3 p-3 bg-orange-50 rounded-lg">
                      <Hotel className="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" />
                      <div>
                        <h4 className="font-semibold text-gray-900 text-sm">{day.hotel.name}</h4>
                        {day.hotel.address && <p className="text-xs text-gray-600">{day.hotel.address}</p>}
                        {day.hotel.nights && <p className="text-xs text-orange-600 mt-1">{day.hotel.nights} nacht(en)</p>}
                      </div>
                    </div>
                  )}
                  
                  {day.highlights.length > 0 && day.highlights.some(h => h.trim()) && (
                    <div className="space-y-1 mb-3">
                      <h4 className="font-semibold text-gray-900 text-sm mb-1">Highlights</h4>
                      {day.highlights.filter(h => h.trim()).map((highlight, hIndex) => (
                        <div key={hIndex} className="flex items-center gap-2 text-gray-700">
                          <span className="text-orange-500">✓</span>
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
        </div>
      </div>

      {/* Media Selector Modal */}
      {showMediaSelector && (
        <SlidingMediaSelector
          isOpen={showMediaSelector}
          onSelect={(url) => {
            if (mediaSelectorMode === 'photo') {
              setHeroImages([...heroImages, url]);
            } else {
              setHeroVideo(url);
            }
            setShowMediaSelector(false);
          }}
          onClose={() => setShowMediaSelector(false)}
        />
      )}
    </div>
  );
}
