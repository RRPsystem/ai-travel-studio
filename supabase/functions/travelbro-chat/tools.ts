/**
 * External API Tools
 * Google Places, Directions, Web Search
 */

export interface Restaurant {
  name: string;
  address: string;
  distance_meters: number;
  rating: number | null;
  price_level: number | null;
  cuisine_types: string[];
  is_open_now: boolean | null;
  google_maps_url: string;
}

export interface RouteInfo {
  distance_km: number;
  duration_minutes: number;
  polyline: string;
  steps: Array<{
    instruction: string;
    distance_meters: number;
    duration_seconds: number;
  }>;
  google_maps_url: string;
}

export class GooglePlacesTool {
  constructor(private apiKey: string) {}

  async findRestaurantsNearby(
    location: string,
    radius: number = 1500
  ): Promise<{ restaurants: Restaurant[]; source: string }> {
    try {
      const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(location)}&key=${this.apiKey}`;
      const geocodeResponse = await fetch(geocodeUrl);
      const geocodeData = await geocodeResponse.json();

      if (geocodeData.status !== 'OK' || !geocodeData.results[0]) {
        return { restaurants: [], source: 'geocode_failed' };
      }

      const { lat, lng } = geocodeData.results[0].geometry.location;

      const placesUrl = `https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=${lat},${lng}&radius=${radius}&type=restaurant&key=${this.apiKey}`;
      const placesResponse = await fetch(placesUrl);
      const placesData = await placesResponse.json();

      if (placesData.status !== 'OK') {
        return { restaurants: [], source: 'places_api_error' };
      }

      const restaurants: Restaurant[] = placesData.results.slice(0, 8).map((place: any) => ({
        name: place.name,
        address: place.vicinity,
        distance_meters: this.calculateDistance(lat, lng, place.geometry.location.lat, place.geometry.location.lng),
        rating: place.rating || null,
        price_level: place.price_level || null,
        cuisine_types: place.types?.filter((t: string) =>
          !['restaurant', 'food', 'point_of_interest', 'establishment'].includes(t)
        ) || [],
        is_open_now: place.opening_hours?.open_now ?? null,
        google_maps_url: `https://www.google.com/maps/place/?q=place_id:${place.place_id}`
      }));

      return { restaurants, source: 'google_places' };
    } catch (error) {
      console.error('Google Places error:', error);
      return { restaurants: [], source: 'error' };
    }
  }

  private calculateDistance(lat1: number, lon1: number, lat2: number, lon2: number): number {
    const R = 6371000;
    const dLat = this.deg2rad(lat2 - lat1);
    const dLon = this.deg2rad(lon2 - lon1);
    const a =
      Math.sin(dLat/2) * Math.sin(dLat/2) +
      Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) *
      Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return Math.round(R * c);
  }

  private deg2rad(deg: number): number {
    return deg * (Math.PI/180);
  }
}

export class GoogleDirectionsTool {
  constructor(private apiKey: string) {}

  async getRoute(
    origin: string,
    destination: string,
    avoidHighways: boolean = false
  ): Promise<RouteInfo | null> {
    try {
      let url = `https://maps.googleapis.com/maps/api/directions/json?origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(destination)}&key=${this.apiKey}`;

      if (avoidHighways) {
        url += '&avoid=highways';
      }

      const response = await fetch(url);
      const data = await response.json();

      if (data.status !== 'OK' || !data.routes[0]) {
        return null;
      }

      const route = data.routes[0];
      const leg = route.legs[0];

      return {
        distance_km: Math.round(leg.distance.value / 1000),
        duration_minutes: Math.round(leg.duration.value / 60),
        polyline: route.overview_polyline.points,
        steps: leg.steps.map((step: any) => ({
          instruction: step.html_instructions.replace(/<[^>]*>/g, ''),
          distance_meters: step.distance.value,
          duration_seconds: step.duration.value
        })),
        google_maps_url: `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(destination)}`
      };
    } catch (error) {
      console.error('Google Directions error:', error);
      return null;
    }
  }
}

export class WebSearchTool {
  constructor(
    private apiKey: string,
    private searchEngineId: string
  ) {}

  async search(query: string, numResults: number = 5): Promise<Array<{title: string; snippet: string; link: string}>> {
    try {
      const url = `https://www.googleapis.com/customsearch/v1?key=${this.apiKey}&cx=${this.searchEngineId}&q=${encodeURIComponent(query)}&num=${numResults}`;

      const response = await fetch(url);
      const data = await response.json();

      if (!data.items) {
        return [];
      }

      return data.items.map((item: any) => ({
        title: item.title,
        snippet: item.snippet,
        link: item.link
      }));
    } catch (error) {
      console.error('Web search error:', error);
      return [];
    }
  }
}
