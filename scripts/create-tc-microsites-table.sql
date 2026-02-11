-- ============================================
-- RUN THIS IN SUPABASE DASHBOARD > SQL EDITOR
-- ============================================

-- Central Travel Compositor credentials table
CREATE TABLE IF NOT EXISTS tc_microsites (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  brand_id UUID REFERENCES brands(id) ON DELETE CASCADE,
  name TEXT NOT NULL,
  microsite_id TEXT NOT NULL,
  username TEXT NOT NULL,
  password TEXT NOT NULL,
  is_active BOOLEAN DEFAULT true,
  last_verified_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_tc_microsites_brand_id ON tc_microsites(brand_id);
CREATE INDEX IF NOT EXISTS idx_tc_microsites_microsite_id ON tc_microsites(microsite_id);

ALTER TABLE tc_microsites ENABLE ROW LEVEL SECURITY;

-- Brand users can manage their own microsites
DROP POLICY IF EXISTS "Brand users can manage own tc_microsites" ON tc_microsites;
CREATE POLICY "Brand users can manage own tc_microsites" ON tc_microsites
  FOR ALL USING (
    brand_id IN (SELECT brand_id FROM users WHERE users.id = auth.uid())
  );

-- Admins/operators can manage all
DROP POLICY IF EXISTS "Admins can manage all tc_microsites" ON tc_microsites;
CREATE POLICY "Admins can manage all tc_microsites" ON tc_microsites
  FOR ALL USING (
    EXISTS (SELECT 1 FROM users WHERE users.id = auth.uid() AND users.role IN ('admin', 'operator'))
  );

-- Edge Functions (service role) need access too - service role bypasses RLS

-- Auto-update updated_at
CREATE OR REPLACE FUNCTION update_tc_microsites_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trigger_update_tc_microsites_updated_at ON tc_microsites;
CREATE TRIGGER trigger_update_tc_microsites_updated_at
  BEFORE UPDATE ON tc_microsites
  FOR EACH ROW
  EXECUTE FUNCTION update_tc_microsites_updated_at();
