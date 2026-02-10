-- Travel Offertes table
CREATE TABLE IF NOT EXISTS travel_offertes (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  brand_id UUID REFERENCES brands(id) ON DELETE CASCADE,
  agent_id UUID REFERENCES users(id) ON DELETE SET NULL,
  travel_compositor_id TEXT,
  
  -- Client info
  client_name TEXT NOT NULL DEFAULT '',
  client_email TEXT,
  client_phone TEXT,
  
  -- Offerte content
  title TEXT NOT NULL DEFAULT 'Nieuwe Offerte',
  subtitle TEXT,
  intro_text TEXT,
  hero_image_url TEXT,
  hero_video_url TEXT,
  
  -- Route map destinations as JSONB array
  destinations JSONB DEFAULT '[]'::jsonb,
  
  -- Itinerary items as JSONB array
  items JSONB DEFAULT '[]'::jsonb,
  
  -- Pricing
  total_price NUMERIC(10,2) DEFAULT 0,
  price_per_person NUMERIC(10,2) DEFAULT 0,
  number_of_travelers INTEGER DEFAULT 2,
  currency TEXT DEFAULT 'EUR',
  
  -- Status
  status TEXT DEFAULT 'draft' CHECK (status IN ('draft', 'sent', 'viewed', 'accepted', 'revised', 'expired')),
  sent_at TIMESTAMPTZ,
  viewed_at TIMESTAMPTZ,
  accepted_at TIMESTAMPTZ,
  valid_until DATE,
  
  -- Notes
  internal_notes TEXT,
  terms_conditions TEXT,
  
  -- Timestamps
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Index for fast lookups
CREATE INDEX idx_travel_offertes_brand_id ON travel_offertes(brand_id);
CREATE INDEX idx_travel_offertes_agent_id ON travel_offertes(agent_id);
CREATE INDEX idx_travel_offertes_status ON travel_offertes(status);

-- RLS policies
ALTER TABLE travel_offertes ENABLE ROW LEVEL SECURITY;

-- Admins and operators can see all
CREATE POLICY "Admins can manage all offertes" ON travel_offertes
  FOR ALL USING (
    EXISTS (SELECT 1 FROM users WHERE users.id = auth.uid() AND users.role IN ('admin', 'operator'))
  );

-- Brands can see their own offertes
CREATE POLICY "Brands can manage own offertes" ON travel_offertes
  FOR ALL USING (
    brand_id IN (SELECT brand_id FROM users WHERE users.id = auth.uid())
  );

-- Agents can see their own offertes
CREATE POLICY "Agents can manage own offertes" ON travel_offertes
  FOR ALL USING (
    agent_id = auth.uid()
  );

-- Updated_at trigger
CREATE OR REPLACE FUNCTION update_travel_offertes_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_travel_offertes_updated_at
  BEFORE UPDATE ON travel_offertes
  FOR EACH ROW
  EXECUTE FUNCTION update_travel_offertes_updated_at();
