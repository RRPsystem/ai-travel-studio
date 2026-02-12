-- Check Google Search API keys in database
SELECT 
  provider, 
  service_name, 
  LENGTH(google_search_api_key) as key_length, 
  LENGTH(google_search_engine_id) as cse_length,
  is_active
FROM api_settings 
WHERE provider IN ('system', 'OpenAI') 
ORDER BY provider, service_name;
