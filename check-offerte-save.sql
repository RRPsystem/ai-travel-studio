-- Check if offertes are being saved correctly
SELECT 
  id,
  title,
  client_name,
  status,
  created_at,
  updated_at
FROM travel_offertes 
ORDER BY updated_at DESC 
LIMIT 5;
