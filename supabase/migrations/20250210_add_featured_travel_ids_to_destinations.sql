-- Add featured_travel_ids column to destinations table
-- Stores an array of travel_compositor_id strings that should be shown as featured travels on the destination page
ALTER TABLE destinations ADD COLUMN IF NOT EXISTS featured_travel_ids text[] DEFAULT '{}';

-- Add a comment for documentation
COMMENT ON COLUMN destinations.featured_travel_ids IS 'Array of Travel Compositor IDs to show as featured travels on this destination page';
