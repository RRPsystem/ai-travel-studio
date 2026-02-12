-- Check if Google Search API keys are configured in the database
SELECT 
  provider,
  service_name,
  google_search_api_key IS NOT NULL as has_search_key,
  google_search_engine_id IS NOT NULL as has_engine_id,
  CASE 
    WHEN google_search_api_key IS NOT NULL THEN LEFT(google_search_api_key, 10) || '...'
    ELSE 'NOT SET'
  END as search_key_preview,
  CASE 
    WHEN google_search_engine_id IS NOT NULL THEN LEFT(google_search_engine_id, 10) || '...'
    ELSE 'NOT SET'
  END as engine_id_preview,
  is_active
FROM api_settings
WHERE provider = 'system' AND service_name = 'Twilio WhatsApp';
