import React, { useEffect, useRef, useState } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

interface Destination {
  name: string;
  country?: string;
  lat?: number;
  lng?: number;
  image?: string;
  description?: string;
  nights?: number;
}

interface RouteMapProps {
  destinations: Destination[];
  height?: string;
  showPolyline?: boolean;
  polylineColor?: string;
  mapboxToken?: string;
  onGeocodingComplete?: (destinations: Destination[]) => void;
}

const DEFAULT_MAPBOX_TOKEN = 'pk.eyJ1Ijoicm9icm9iYXMiLCJhIjoiY200djkxd2drMDA5dTJqc2Q1MXpvYTR6NCJ9.XEsEVOmAPcOUkfcTms-VbA';

// Geocode a destination name using Nominatim (free, no API key needed)
async function geocodeAddress(address: string): Promise<[number, number] | null> {
  try {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`;
    const response = await fetch(url, {
      headers: { 'User-Agent': 'TravelCStudio/1.0' }
    });
    const data = await response.json();
    if (data && data.length > 0) {
      return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
    }
    return null;
  } catch (error) {
    console.error('Geocoding error for:', address, error);
    return null;
  }
}

// Create numbered marker icon using canvas (same approach as RBS plugin)
function createNumberedIcon(number: number): L.DivIcon {
  return L.divIcon({
    html: `<div style="
      background-color: #2A81CB;
      color: white;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      border: 2px solid white;
      box-shadow: 0 0 4px rgba(0,0,0,0.3);
      font-size: 14px;
    ">${number}</div>`,
    className: '',
    iconSize: [30, 30],
    iconAnchor: [15, 15],
  });
}

export function RouteMap({
  destinations,
  height = '400px',
  showPolyline = true,
  polylineColor = '#2A81CB',
  mapboxToken = DEFAULT_MAPBOX_TOKEN,
  onGeocodingComplete,
}: RouteMapProps) {
  const mapRef = useRef<HTMLDivElement>(null);
  const mapInstanceRef = useRef<L.Map | null>(null);
  const [geocodedDests, setGeocodedDests] = useState<Destination[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Geocode destinations that don't have lat/lng
  useEffect(() => {
    if (!destinations || destinations.length === 0) {
      setLoading(false);
      setError('Geen bestemmingen beschikbaar');
      return;
    }

    let cancelled = false;

    async function geocodeAll() {
      setLoading(true);
      setError(null);
      const results: Destination[] = [];

      for (const dest of destinations) {
        if (cancelled) return;

        if (dest.lat && dest.lng && dest.lat !== 0 && dest.lng !== 0) {
          results.push(dest);
          continue;
        }

        // Geocode using name + country
        const searchQuery = dest.country ? `${dest.name}, ${dest.country}` : dest.name;
        const coords = await geocodeAddress(searchQuery);

        if (coords) {
          results.push({ ...dest, lat: coords[0], lng: coords[1] });
        } else {
          console.warn(`Could not geocode: ${searchQuery}`);
        }

        // Nominatim rate limit: 1 request per second
        await new Promise(resolve => setTimeout(resolve, 1100));
      }

      if (!cancelled) {
        setGeocodedDests(results);
        setLoading(false);

        if (results.length < 2) {
          setError('Niet genoeg bestemmingen met coördinaten gevonden');
        }

        if (onGeocodingComplete && results.length > 0) {
          onGeocodingComplete(results);
        }
      }
    }

    geocodeAll();
    return () => { cancelled = true; };
  }, [destinations]);

  // Render map when geocoded destinations are ready
  useEffect(() => {
    if (!mapRef.current || geocodedDests.length < 2 || loading) return;

    // Clean up previous map
    if (mapInstanceRef.current) {
      mapInstanceRef.current.remove();
      mapInstanceRef.current = null;
    }

    const validDests = geocodedDests.filter(d => d.lat && d.lng);
    if (validDests.length < 2) return;

    // Create map
    const map = L.map(mapRef.current).setView(
      [validDests[0].lat!, validDests[0].lng!],
      6
    );

    // Mapbox tiles (same as RBS plugin)
    const tileUrl = `https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=${mapboxToken}&language=nl`;
    L.tileLayer(tileUrl, {
      maxZoom: 18,
      attribution: '&copy; <a href="https://www.mapbox.com/about/maps/">Mapbox</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      id: 'mapbox/streets-v11',
    } as any).addTo(map);

    // Add markers
    const markerGroup = L.featureGroup();
    const latlngs: [number, number][] = [];

    validDests.forEach((dest, idx) => {
      const icon = createNumberedIcon(idx + 1);
      const marker = L.marker([dest.lat!, dest.lng!], { icon });

      // Popup with image and description
      let popupHtml = `<div style="max-width:250px">`;
      popupHtml += `<h3 style="margin:0 0 4px;font-size:14px;font-weight:600">${dest.name}</h3>`;
      if (dest.nights) {
        popupHtml += `<div style="font-size:12px;color:#666;margin-bottom:4px">${dest.nights} nachten</div>`;
      }
      if (dest.image) {
        popupHtml += `<img src="${dest.image}" style="width:100%;max-height:120px;object-fit:cover;border-radius:6px;margin-bottom:4px" />`;
      }
      if (dest.description) {
        const plainText = dest.description.replace(/<[^>]*>/g, '').substring(0, 150);
        popupHtml += `<p style="font-size:12px;color:#555;margin:0">${plainText}...</p>`;
      }
      popupHtml += `</div>`;

      marker.bindPopup(popupHtml);
      marker.addTo(markerGroup);
      latlngs.push([dest.lat!, dest.lng!]);
    });

    markerGroup.addTo(map);

    // Add polyline route
    if (showPolyline && latlngs.length >= 2) {
      L.polyline(latlngs, { color: polylineColor, weight: 3, opacity: 0.7 }).addTo(map);
    }

    // Fit bounds
    map.fitBounds(markerGroup.getBounds(), { padding: [30, 30] });

    mapInstanceRef.current = map;

    return () => {
      if (mapInstanceRef.current) {
        mapInstanceRef.current.remove();
        mapInstanceRef.current = null;
      }
    };
  }, [geocodedDests, loading, showPolyline, polylineColor, mapboxToken]);

  if (error && geocodedDests.length < 2) {
    return (
      <div className="flex items-center justify-center bg-gray-100 rounded-lg" style={{ height }}>
        <p className="text-gray-500 text-sm">{error}</p>
      </div>
    );
  }

  return (
    <div className="relative">
      {loading && (
        <div className="absolute inset-0 flex items-center justify-center bg-gray-100 rounded-lg z-10" style={{ height }}>
          <div className="text-center">
            <div className="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-2"></div>
            <p className="text-sm text-gray-600">Bestemmingen geocoderen...</p>
            <p className="text-xs text-gray-400 mt-1">{geocodedDests.length}/{destinations.length} gevonden</p>
          </div>
        </div>
      )}
      <div
        ref={mapRef}
        style={{ height, width: '100%', borderRadius: '12px' }}
      />
      {!loading && geocodedDests.length >= 2 && (
        <div className="mt-2 flex items-center gap-4 text-xs text-gray-500">
          <span>{geocodedDests.length} bestemmingen op kaart</span>
          {geocodedDests.length < destinations.length && (
            <span className="text-orange-500">
              {destinations.length - geocodedDests.length} bestemming(en) niet gevonden
            </span>
          )}
        </div>
      )}
    </div>
  );
}
