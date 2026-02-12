-- Test TC Microsites vanuit database
-- Run dit in Supabase SQL Editor om te verifiëren dat credentials correct zijn

-- 1. Check welke microsites er zijn
SELECT 
    microsite_id,
    name,
    username,
    is_active,
    created_at
FROM tc_microsites
WHERE is_active = true
ORDER BY created_at DESC;

-- 2. Check of rondreis-planner erin staat
SELECT 
    microsite_id,
    name,
    username,
    CASE 
        WHEN password IS NOT NULL AND password != '' THEN '✓ Password set'
        ELSE '✗ No password'
    END as password_status,
    is_active
FROM tc_microsites
WHERE microsite_id LIKE '%rondreis%'
   OR microsite_id LIKE '%planner%';

-- 3. Als rondreis-planner er NIET in staat, voeg toe:
-- UNCOMMENT en vul in met jouw credentials:
/*
INSERT INTO tc_microsites (microsite_id, name, username, password, is_active)
VALUES (
    'rondreis-planner',
    'Rondreis Planner',
    'jouw_tc_username_hier',
    'jouw_tc_password_hier',
    true
)
ON CONFLICT (microsite_id) 
DO UPDATE SET
    username = EXCLUDED.username,
    password = EXCLUDED.password,
    is_active = EXCLUDED.is_active,
    updated_at = NOW();
*/
