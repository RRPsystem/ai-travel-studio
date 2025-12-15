/*
  # Centralized Trip Catalog System
  
  ## Overzicht
  Dit systeem maakt het mogelijk voor Tour Operator brands om reizen door te sturen naar
  een centrale Admin catalogus, waar Admin ze kan goedkeuren en doorsturen naar andere brands.
  
  ## Workflow
  1. Tour Operator (of andere Operator brand) maakt reis aan
  2. Tour Operator zet `submit_to_catalog` flag op true
  3. Admin ziet deze reis in hun catalogus overzicht
  4. Admin keurt de reis goed via `catalog_status`
  5. Admin kan goedgekeurde reis toewijzen aan brands via trip_brand_assignments
  
  ## Changes
  
  1. **Add catalog columns to trips table**
     - `submit_to_catalog` (boolean) - Of de reis is ingediend voor centrale catalogus
     - `catalog_status` (text) - Status: 'pending', 'approved', 'rejected'
     - `catalog_submitted_at` (timestamptz) - Wanneer ingediend
     - `catalog_reviewed_at` (timestamptz) - Wanneer beoordeeld door admin
     - `catalog_reviewed_by` (uuid) - Welke admin heeft beoordeeld
     - `catalog_notes` (text) - Notities van admin
  
  2. **Update RLS policies**
     - Operator brands kunnen submit_to_catalog flag zetten
     - Admin kan catalog_status aanpassen
     - Admin kan alle catalog submissions zien
  
  ## Security
  - RLS policies zorgen dat alleen operators kunnen submitten
  - Alleen admins kunnen catalog status aanpassen
  - Brands zien alleen hun eigen reizen + admin assigned reizen
*/

-- Add submit_to_catalog column
ALTER TABLE trips 
ADD COLUMN IF NOT EXISTS submit_to_catalog boolean DEFAULT false;

-- Add catalog_status column
ALTER TABLE trips 
ADD COLUMN IF NOT EXISTS catalog_status text 
CHECK (catalog_status IN ('pending', 'approved', 'rejected'));

-- Add catalog_submitted_at column
ALTER TABLE trips 
ADD COLUMN IF NOT EXISTS catalog_submitted_at timestamptz;

-- Add catalog_reviewed_at column
ALTER TABLE trips 
ADD COLUMN IF NOT EXISTS catalog_reviewed_at timestamptz;

-- Add catalog_reviewed_by column
ALTER TABLE trips 
ADD COLUMN IF NOT EXISTS catalog_reviewed_by uuid REFERENCES auth.users(id) ON DELETE SET NULL;

-- Add catalog_notes column for admin feedback
ALTER TABLE trips 
ADD COLUMN IF NOT EXISTS catalog_notes text;

-- Add helpful comments
COMMENT ON COLUMN trips.submit_to_catalog IS 'When true, this trip is submitted to the central admin catalog for approval';
COMMENT ON COLUMN trips.catalog_status IS 'Admin approval status: pending, approved, or rejected';
COMMENT ON COLUMN trips.catalog_submitted_at IS 'Timestamp when trip was submitted to catalog';
COMMENT ON COLUMN trips.catalog_reviewed_at IS 'Timestamp when admin reviewed the catalog submission';
COMMENT ON COLUMN trips.catalog_reviewed_by IS 'Admin user who reviewed the catalog submission';
COMMENT ON COLUMN trips.catalog_notes IS 'Admin notes about the catalog submission';

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_trips_submit_to_catalog ON trips(submit_to_catalog) WHERE submit_to_catalog = true;
CREATE INDEX IF NOT EXISTS idx_trips_catalog_status ON trips(catalog_status);
CREATE INDEX IF NOT EXISTS idx_trips_catalog_submitted_at ON trips(catalog_submitted_at);

-- Update RLS policy for viewing trips to include catalog submissions
DROP POLICY IF EXISTS "Users can view trips for their brand or admin trips" ON trips;

CREATE POLICY "Users can view trips for their brand or admin trips"
  ON trips FOR SELECT
  TO authenticated
  USING (
    -- Own brand trips
    brand_id IN (
      SELECT brand_id FROM users WHERE id = auth.uid()
    )
    -- OR admin trips
    OR author_type = 'admin'
    -- OR system brand trips
    OR brand_id = '00000000-0000-0000-0000-000000000999'
    -- OR admin users can see catalog submissions
    OR (
      submit_to_catalog = true
      AND EXISTS (
        SELECT 1 FROM users WHERE id = auth.uid() AND role = 'admin'
      )
    )
  );

-- Update RLS policy for updating trips to allow operators to submit to catalog
DROP POLICY IF EXISTS "Users can update trips for their brand" ON trips;

CREATE POLICY "Users can update trips for their brand"
  ON trips FOR UPDATE
  TO authenticated
  USING (
    -- Own brand trips
    brand_id IN (
      SELECT brand_id FROM users WHERE id = auth.uid()
    )
    -- OR admin trips they created
    OR (
      author_type = 'admin' AND author_id = auth.uid()
    )
    -- OR admin reviewing catalog
    OR (
      submit_to_catalog = true
      AND EXISTS (
        SELECT 1 FROM users WHERE id = auth.uid() AND role = 'admin'
      )
    )
    -- OR operator role can update their submissions
    OR (
      submit_to_catalog = true
      AND brand_id IN (
        SELECT brand_id FROM users WHERE id = auth.uid() AND role = 'operator'
      )
      AND catalog_status IS NULL -- Only before admin review
    )
  )
  WITH CHECK (
    -- Own brand trips
    brand_id IN (
      SELECT brand_id FROM users WHERE id = auth.uid()
    )
    -- OR admin trips they created
    OR (
      author_type = 'admin' AND author_id = auth.uid()
    )
    -- OR admin can update catalog status
    OR (
      submit_to_catalog = true
      AND EXISTS (
        SELECT 1 FROM users WHERE id = auth.uid() AND role = 'admin'
      )
    )
  );

-- Create a policy specifically for operators to submit to catalog
CREATE POLICY "Operators can submit trips to catalog"
  ON trips FOR UPDATE
  TO authenticated
  USING (
    brand_id IN (
      SELECT u.brand_id 
      FROM users u
      JOIN brands b ON b.id = u.brand_id
      WHERE u.id = auth.uid() 
      AND u.role = 'operator'
      AND b.business_type = 'tour_operator'
    )
  )
  WITH CHECK (
    brand_id IN (
      SELECT u.brand_id 
      FROM users u
      JOIN brands b ON b.id = u.brand_id
      WHERE u.id = auth.uid() 
      AND u.role = 'operator'
      AND b.business_type = 'tour_operator'
    )
  );
