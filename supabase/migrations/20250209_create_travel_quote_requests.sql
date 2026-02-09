-- Create travel_quote_requests table for storing quote requests from WordPress
CREATE TABLE IF NOT EXISTS travel_quote_requests (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  brand_id uuid NOT NULL REFERENCES brands(id) ON DELETE CASCADE,
  travel_id text,
  travel_title text,
  travel_url text,
  
  -- Customer info
  customer_name text NOT NULL,
  customer_email text NOT NULL,
  customer_phone text,
  
  -- Request details
  departure_date date,
  number_of_persons integer DEFAULT 2,
  request_type text DEFAULT 'quote' CHECK (request_type IN ('quote', 'info')),
  message text,
  
  -- Status tracking
  status text DEFAULT 'new' CHECK (status IN ('new', 'read', 'contacted', 'quoted', 'booked', 'cancelled')),
  notes text,
  
  -- Source
  source_url text,
  source_ip text,
  
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Indexes
CREATE INDEX IF NOT EXISTS idx_quote_requests_brand ON travel_quote_requests(brand_id);
CREATE INDEX IF NOT EXISTS idx_quote_requests_status ON travel_quote_requests(status);
CREATE INDEX IF NOT EXISTS idx_quote_requests_created ON travel_quote_requests(created_at DESC);

-- Enable RLS
ALTER TABLE travel_quote_requests ENABLE ROW LEVEL SECURITY;

-- Admin can view all
CREATE POLICY "Admin can view all quote requests"
  ON travel_quote_requests FOR SELECT
  TO authenticated
  USING (
    EXISTS (
      SELECT 1 FROM users WHERE users.id = auth.uid() AND users.role = 'admin'
    )
  );

-- Brands can view their own
CREATE POLICY "Brands can view own quote requests"
  ON travel_quote_requests FOR SELECT
  TO authenticated
  USING (
    brand_id IN (
      SELECT brand_id FROM users WHERE users.id = auth.uid()
    )
  );

-- Admin can update all
CREATE POLICY "Admin can update all quote requests"
  ON travel_quote_requests FOR UPDATE
  TO authenticated
  USING (
    EXISTS (
      SELECT 1 FROM users WHERE users.id = auth.uid() AND users.role = 'admin'
    )
  );

-- Brands can update their own
CREATE POLICY "Brands can update own quote requests"
  ON travel_quote_requests FOR UPDATE
  TO authenticated
  USING (
    brand_id IN (
      SELECT brand_id FROM users WHERE users.id = auth.uid()
    )
  );

-- Allow inserts from Edge Functions (service role) - no auth needed for public form submissions
CREATE POLICY "Service role can insert quote requests"
  ON travel_quote_requests FOR INSERT
  TO authenticated, anon
  WITH CHECK (true);

-- Updated_at trigger
CREATE OR REPLACE FUNCTION update_quote_request_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_quote_request_updated_at
  BEFORE UPDATE ON travel_quote_requests
  FOR EACH ROW
  EXECUTE FUNCTION update_quote_request_updated_at();
