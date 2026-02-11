/*
  # Fix social_media_posts INSERT policy for Admin

  This allows Admin users to create posts for any brand, especially for sharing with Brands/Agents.

  ## Changes
  1. Drop existing INSERT policy
  2. Create new policy that allows:
     - Brand members to create posts for their own brand
     - Admin users to create posts for any brand (for sharing functionality)
*/

-- Drop existing policy
DROP POLICY IF EXISTS "Brand members can create social media posts" ON social_media_posts;

-- Create new policy that allows Admin to create posts for any brand
CREATE POLICY "Users can create social media posts"
  ON social_media_posts FOR INSERT
  TO authenticated
  WITH CHECK (
    -- Allow if user is creating for their own brand
    (brand_id IN (
      SELECT brand_id FROM users WHERE id = auth.uid()
    ) AND created_by = auth.uid())
    OR
    -- Allow if user is Admin (can create for any brand)
    (EXISTS (
      SELECT 1 FROM users WHERE id = auth.uid() AND role = 'admin'
    ) AND created_by = auth.uid())
  );

COMMENT ON POLICY "Users can create social media posts" ON social_media_posts IS 
  'Allows brand members to create posts for their brand, and Admin to create posts for any brand (for sharing with Brands/Agents)';
