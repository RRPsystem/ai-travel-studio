-- Fix: Brand users moeten ook assignments kunnen aanmaken voor hun eigen brand
-- Voorheen was INSERT alleen toegestaan voor admin/super_admin

DROP POLICY IF EXISTS "Admins can insert assignments" ON travelc_travel_brand_assignments;

CREATE POLICY "Users can insert own brand assignments"
  ON travelc_travel_brand_assignments FOR INSERT
  TO authenticated
  WITH CHECK (
    brand_id IN (
      SELECT brand_id FROM users WHERE id = auth.uid()
    )
    OR EXISTS (
      SELECT 1 FROM users 
      WHERE id = auth.uid() 
      AND role IN ('admin', 'super_admin')
    )
  );
