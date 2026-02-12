import { useRef, useState, useEffect } from 'react';
import { MapPin, Building2, Utensils, ShoppingBag, Compass, Ticket } from 'lucide-react';
import { OfferteDestination, OfferteItem } from '../../types/offerte';

interface DayByDaySectionProps {
  destinations: OfferteDestination[];
  items: OfferteItem[];
  brandColor?: string;
}

// Red car SVG component
function CarIcon({ className }: { className?: string }) {
  return (
    <svg viewBox="0 0 40 24" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="2" y="8" width="36" height="12" rx="3" fill="#dc2626" />
      <path d="M8 8 L12 2 L28 2 L32 8" fill="#dc2626" stroke="#dc2626" strokeWidth="1" />
      <rect x="13" y="3" width="6" height="5" rx="1" fill="#87CEEB" opacity="0.8" />
      <rect x="21" y="3" width="6" height="5" rx="1" fill="#87CEEB" opacity="0.8" />
      <circle cx="10" cy="20" r="3.5" fill="#333" />
      <circle cx="10" cy="20" r="1.5" fill="#888" />
      <circle cx="30" cy="20" r="3.5" fill="#333" />
      <circle cx="30" cy="20" r="1.5" fill="#888" />
      <rect x="34" y="10" width="4" height="3" rx="1" fill="#ff6b6b" opacity="0.9" />
      <rect x="2" y="11" width="3" height="2" rx="1" fill="#ffd700" opacity="0.9" />
    </svg>
  );
}

// Match hotels to destinations by checking date overlap or name
function matchHotelToDestination(dest: OfferteDestination, items: OfferteItem[]): OfferteItem | null {
  const hotels = items.filter(i => i.type === 'hotel');
  // Try matching by location/name
  for (const h of hotels) {
    const hotelLoc = (h.location || h.hotel_name || h.title || '').toLowerCase();
    const destName = (dest.name || '').toLowerCase();
    if (hotelLoc && destName && (hotelLoc.includes(destName) || destName.includes(hotelLoc))) {
      return h;
    }
  }
  // Fallback: match by order (destination index = hotel index)
  return null;
}

export function DayByDaySection({ destinations, items, brandColor = '#2e7d32' }: DayByDaySectionProps) {
  const roadRef = useRef<HTMLDivElement>(null);
  const [carTop, setCarTop] = useState(0);

  // Scroll-based car position
  useEffect(() => {
    const handleScroll = () => {
      if (!roadRef.current) return;
      const rect = roadRef.current.getBoundingClientRect();
      const roadHeight = roadRef.current.scrollHeight;
      const viewportH = window.innerHeight;

      // Calculate how far we've scrolled through the road section
      const sectionTop = rect.top + window.scrollY;
      const scrollProgress = Math.max(0, Math.min(1,
        (window.scrollY + viewportH * 0.5 - sectionTop) / roadHeight
      ));
      setCarTop(scrollProgress * 100);
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  if (!destinations.length) return null;

  // Match hotels to destinations by index as fallback
  const hotels = items.filter(i => i.type === 'hotel');

  return (
    <div className="w-full">
      {/* Section header */}
      <div className="max-w-6xl mx-auto px-6 pt-12 pb-6">
        <h2 className="text-2xl font-bold text-gray-900">Dag voor Dag</h2>
        <p className="text-sm text-gray-500 mt-1">{destinations.length} bestemmingen op deze rondreis</p>
      </div>

      {/* Day-by-day road layout */}
      <div ref={roadRef} className="relative max-w-6xl mx-auto">

        {/* === CENTRAL ROAD === */}
        <div className="absolute left-1/2 top-0 bottom-0 w-10 -translate-x-1/2 z-10 pointer-events-none">
          {/* Road surface */}
          <div className="absolute inset-0 bg-gray-500 rounded-sm" />
          {/* Dashed center line */}
          <div
            className="absolute left-1/2 top-0 bottom-0 w-0.5 -translate-x-1/2"
            style={{
              backgroundImage: 'repeating-linear-gradient(to bottom, white 0px, white 12px, transparent 12px, transparent 24px)',
            }}
          />
        </div>

        {/* === SCROLLING CAR === */}
        <div
          className="absolute left-1/2 -translate-x-1/2 z-20 pointer-events-none transition-all duration-150 ease-out"
          style={{ top: `clamp(20px, ${carTop}%, calc(100% - 60px))` }}
        >
          <div className="w-10 flex items-center justify-center">
            <CarIcon className="w-9 h-6 rotate-90" />
          </div>
        </div>

        {/* === DESTINATION STOPS === */}
        {destinations.map((dest, idx) => {
          const isEven = idx % 2 === 0; // even = photo LEFT, text RIGHT
          const matchedHotel = matchHotelToDestination(dest, items) || hotels[idx] || null;
          const hasImage = dest.images && dest.images.length > 0;
          const dayLabel = dest.order !== undefined ? dest.order + 1 : idx + 1;

          return (
            <div key={idx} className="relative">
              {/* Stop marker on the road */}
              <div className="absolute left-1/2 -translate-x-1/2 z-30" style={{ top: '40px' }}>
                <div
                  className="w-14 h-14 rounded-full border-4 border-white shadow-lg flex items-center justify-center"
                  style={{ backgroundColor: brandColor }}
                >
                  <span className="text-white text-xs font-bold leading-none text-center">
                    Stop<br />{idx + 1}
                  </span>
                </div>
              </div>

              {/* Two-column layout */}
              <div className={`flex ${isEven ? 'flex-row' : 'flex-row-reverse'}`}>
                {/* === PHOTO HALF === */}
                <div className="w-[calc(50%-20px)] relative min-h-[400px]">
                  {hasImage ? (
                    <img
                      src={dest.images![0]}
                      alt={dest.name}
                      className="w-full h-full object-cover"
                      style={{ minHeight: '400px' }}
                    />
                  ) : (
                    <div className="w-full h-full min-h-[400px] bg-gray-200 flex items-center justify-center">
                      <MapPin size={48} className="text-gray-400" />
                    </div>
                  )}
                  {/* Photo navigation for multiple images */}
                  {dest.images && dest.images.length > 1 && (
                    <div className="absolute bottom-4 left-4 flex gap-1.5">
                      {dest.images.slice(0, 5).map((_, i) => (
                        <div key={i} className={`w-2 h-2 rounded-full ${i === 0 ? 'bg-white' : 'bg-white/50'}`} />
                      ))}
                    </div>
                  )}
                </div>

                {/* === GAP FOR ROAD === */}
                <div className="w-10 shrink-0" />

                {/* === TEXT HALF === */}
                <div className="w-[calc(50%-20px)] flex flex-col">
                  {/* Content */}
                  <div className="flex-1 p-8 bg-white">
                    <h3 className="text-2xl font-bold text-gray-900 mb-1">
                      Dag {dayLabel}: {dest.name}
                    </h3>
                    {dest.country && (
                      <p className="text-sm text-gray-400 italic mb-4">{dest.country}</p>
                    )}

                    {dest.description && (
                      <p className="text-sm text-gray-600 leading-relaxed mb-6">{dest.description}</p>
                    )}

                    {/* Info grid - highlights or generic */}
                    {dest.highlights && dest.highlights.length > 0 ? (
                      <div className="grid grid-cols-2 gap-4 mt-4">
                        {dest.highlights.slice(0, 4).map((h, i) => {
                          const icons = [
                            { icon: MapPin, label: 'Bezienswaardigheid' },
                            { icon: ShoppingBag, label: 'Lokale producten' },
                            { icon: Utensils, label: 'Lokale gerechten' },
                            { icon: Ticket, label: 'Activiteiten' },
                          ];
                          const { icon: Icon, label } = icons[i % icons.length];
                          return (
                            <div key={i} className="flex items-start gap-2">
                              <Icon size={16} style={{ color: brandColor }} className="mt-0.5 shrink-0" />
                              <div>
                                <div className="text-xs font-semibold text-gray-800">{h}</div>
                                <div className="text-[10px] text-gray-400">{label}</div>
                              </div>
                            </div>
                          );
                        })}
                      </div>
                    ) : (
                      <div className="grid grid-cols-2 gap-4 mt-4">
                        <div className="flex items-start gap-2">
                          <MapPin size={16} style={{ color: brandColor }} className="mt-0.5 shrink-0" />
                          <div>
                            <div className="text-xs font-semibold text-gray-800">Tourist Attraction:</div>
                            <div className="text-[10px] text-gray-400">Bezienswaardigheden</div>
                          </div>
                        </div>
                        <div className="flex items-start gap-2">
                          <ShoppingBag size={16} style={{ color: brandColor }} className="mt-0.5 shrink-0" />
                          <div>
                            <div className="text-xs font-semibold text-gray-800">Best Buy:</div>
                            <div className="text-[10px] text-gray-400">Lokale producten</div>
                          </div>
                        </div>
                        <div className="flex items-start gap-2">
                          <Utensils size={16} style={{ color: brandColor }} className="mt-0.5 shrink-0" />
                          <div>
                            <div className="text-xs font-semibold text-gray-800">Food Speciality:</div>
                            <div className="text-[10px] text-gray-400">Lokale gerechten</div>
                          </div>
                        </div>
                        <div className="flex items-start gap-2">
                          <Compass size={16} style={{ color: brandColor }} className="mt-0.5 shrink-0" />
                          <div>
                            <div className="text-xs font-semibold text-gray-800">Activity:</div>
                            <div className="text-[10px] text-gray-400">Activiteiten</div>
                          </div>
                        </div>
                      </div>
                    )}
                  </div>

                  {/* Hotel bar at bottom */}
                  {matchedHotel && (
                    <div
                      className="px-6 py-3 flex items-center gap-3 text-white text-sm"
                      style={{ backgroundColor: brandColor }}
                    >
                      <Building2 size={16} className="shrink-0" />
                      <div>
                        <span className="text-[10px] uppercase tracking-wider opacity-70">Hotel</span>
                        <span className="mx-2 font-medium">{matchedHotel.title || matchedHotel.hotel_name}</span>
                        {matchedHotel.location && (
                          <span className="opacity-70 text-xs">, {matchedHotel.location}</span>
                        )}
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
