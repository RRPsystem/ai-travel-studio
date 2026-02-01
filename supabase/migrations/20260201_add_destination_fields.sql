-- Add missing columns to destinations table for AI-generated content
ALTER TABLE destinations ADD COLUMN IF NOT EXISTS best_time_to_visit TEXT;
ALTER TABLE destinations ADD COLUMN IF NOT EXISTS currency TEXT;
ALTER TABLE destinations ADD COLUMN IF NOT EXISTS language TEXT;
ALTER TABLE destinations ADD COLUMN IF NOT EXISTS timezone TEXT;
ALTER TABLE destinations ADD COLUMN IF NOT EXISTS visa_info TEXT;
ALTER TABLE destinations ADD COLUMN IF NOT EXISTS highlights JSONB DEFAULT '[]'::jsonb;
ALTER TABLE destinations ADD COLUMN IF NOT EXISTS regions JSONB DEFAULT '[]'::jsonb;
ALTER TABLE destinations ADD COLUMN IF NOT EXISTS facts JSONB DEFAULT '[]'::jsonb;
