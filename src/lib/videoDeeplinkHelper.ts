/**
 * Video Generator Deeplink Helper
 *
 * Genereer deeplinks naar de AI Video Generator voor brands en agents
 */

export type VideoDeeplinkMode = 'new' | 'edit' | 'trip';

export interface VideoDeeplinkOptions {
  videoSlug?: string;
  videoId?: string;
  agentId?: string;
  tripId?: string;
}

/**
 * Genereer deeplink URL naar video generator
 *
 * @param mode - 'new' voor nieuwe video, 'edit' voor bestaande, 'trip' voor trip video
 * @param brandId - UUID van de brand
 * @param token - JWT token met video scopes
 * @param options - Extra parameters zoals videoSlug, agentId, tripId
 * @returns Volledige deeplink URL
 *
 * @example
 * // Nieuwe video maken
 * const url = generateVideoDeeplink('new', brandId, token);
 *
 * @example
 * // Bestaande video bewerken
 * const url = generateVideoDeeplink('edit', brandId, token, {
 *   videoSlug: 'zuid-afrika-safari'
 * });
 *
 * @example
 * // Video voor trip maken
 * const url = generateVideoDeeplink('trip', brandId, token, {
 *   agentId: 'agent-uuid',
 *   tripId: 'trip-uuid'
 * });
 */
export const generateVideoDeeplink = (
  brandId: string,
  mode: VideoDeeplinkMode | string = 'new',
  options: VideoDeeplinkOptions = {}
): string => {
  // Base URL van de AI Travel Video (website-builder project)
  const base = 'https://www.ai-websitestudio.nl/generator.html';

  // Build query parameters
  const params = new URLSearchParams({
    brand_id: brandId,
    return_url: window.location.href
  });

  // Add mode-specific parameters
  if (mode === 'edit') {
    if (options.videoSlug) {
      params.append('slug', options.videoSlug);
    }
    if (options.videoId) {
      params.append('video_id', options.videoId);
    }
  }

  if (mode === 'trip') {
    if (options.agentId) {
      params.append('agent_id', options.agentId);
    }
    if (options.tripId) {
      params.append('trip_id', options.tripId);
      params.append('title', options.tripId);
    }
  }

  return `${base}?${params.toString()}`;
};

/**
 * JWT Token Scopes voor video generator
 */
export const VIDEO_JWT_SCOPES = {
  brand: ['videos:read', 'videos:write', 'videos:generate'],
  agent: ['videos:read', 'videos:write']
};

/**
 * Genereer JWT token voor video generator
 *
 * @param brandId - UUID van de brand
 * @param scopes - Array van scopes (videos:read, videos:write, videos:generate)
 * @returns Promise met JWT token
 */
export const generateVideoJWT = async (
  brandId: string,
  scopes: string[]
): Promise<string> => {
  const response = await fetch(
    `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/generate-builder-jwt`,
    {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        brand_id: brandId,
        scopes
      })
    }
  );

  if (!response.ok) {
    throw new Error('Failed to generate JWT token');
  }

  const data = await response.json();
  return data.token;
};

/**
 * Open video generator in nieuwe tab
 *
 * @param brandId - UUID van de brand
 * @param mode - Video generator mode
 * @param options - Extra parameters
 */
export const openVideoGenerator = (
  brandId: string,
  mode: VideoDeeplinkMode | string = 'new',
  options: VideoDeeplinkOptions = {}
): void => {
  const deeplink = generateVideoDeeplink(brandId, mode, options);
  window.open(deeplink, '_blank');
};
