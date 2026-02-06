-- Create travelc_categories table for admin-managed travel categories
CREATE TABLE IF NOT EXISTS travelc_categories (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  name TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  icon TEXT DEFAULT '📁',
  color TEXT DEFAULT '#3B82F6',
  description TEXT,
  sort_order INTEGER DEFAULT 0,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Insert default categories
INSERT INTO travelc_categories (name, slug, icon, color, sort_order) VALUES
  ('Rondreis', 'rondreis', '🚗', '#3B82F6', 1),
  ('Fly & Drive', 'fly-drive', '✈️', '#10B981', 2),
  ('Stedentrip', 'stedentrip', '🏙️', '#8B5CF6', 3),
  ('Strandvakantie', 'strandvakantie', '🏖️', '#F59E0B', 4),
  ('Avontuur', 'avontuur', '🏔️', '#EF4444', 5),
  ('Luxe', 'luxe', '💎', '#EC4899', 6),
  ('Budget', 'budget', '💰', '#22C55E', 7),
  ('Familie', 'familie', '👨‍👩‍👧‍👦', '#06B6D4', 8),
  ('Romantisch', 'romantisch', '❤️', '#F43F5E', 9),
  ('Actief', 'actief', '🚴', '#84CC16', 10),
  ('Cruise', 'cruise', '🚢', '#0EA5E9', 11),
  ('Safari', 'safari', '🦁', '#D97706', 12)
ON CONFLICT (slug) DO NOTHING;

-- Enable RLS
ALTER TABLE travelc_categories ENABLE ROW LEVEL SECURITY;

-- RLS policies - everyone can read, only operators can write
CREATE POLICY "Anyone can read categories" ON travelc_categories
  FOR SELECT USING (true);

CREATE POLICY "Operators can manage categories" ON travelc_categories
  FOR ALL USING (
    EXISTS (
      SELECT 1 FROM profiles 
      WHERE profiles.id = auth.uid() 
      AND profiles.role = 'operator'
    )
  );
