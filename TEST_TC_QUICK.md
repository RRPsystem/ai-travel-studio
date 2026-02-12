# Quick Test - Travel Compositor API

## Stap 1: Test Authenticatie Direct

Open PowerShell en voer uit (vervang USERNAME en PASSWORD):

```powershell
$username = "jouw_username"
$password = "jouw_password"
$micrositeId = "rondreis-planner"

$authBody = @{
    username = $username
    password = $password
    micrositeId = $micrositeId
} | ConvertTo-Json

$auth = Invoke-RestMethod -Uri "https://online.travelcompositor.com/resources/authentication/authenticate" `
    -Method POST `
    -Headers @{
        "Content-Type" = "application/json"
        "Accept-Encoding" = "gzip"
    } `
    -Body $authBody

Write-Host "Token: $($auth.token.Substring(0, 50))..."
Write-Host "Expires in: $($auth.expirationInSeconds) seconds"
```

**Verwacht resultaat:** Je krijgt een token terug

---

## Stap 2: Test Hotel Quote

Gebruik de token uit stap 1:

```powershell
$token = $auth.token

$quoteBody = @{
    checkIn = "2026-07-15"
    checkOut = "2026-07-18"
    distributions = @(
        @{
            persons = @(
                @{ age = 30 },
                @{ age = 30 }
            )
        }
    )
    language = "EN"
    sourceMarket = "ES"
    filter = @{
        bestCombinations = $true
        maxCombinations = 5
        includeOnRequestOptions = $true
    }
    timeout = 5000
    destinationId = "AMS"
} | ConvertTo-Json -Depth 10

try {
    $quote = Invoke-RestMethod -Uri "https://online.travelcompositor.com/resources/booking/accommodations/quote" `
        -Method POST `
        -Headers @{
            "Content-Type" = "application/json"
            "auth-token" = $token
            "Accept-Encoding" = "gzip"
        } `
        -Body $quoteBody
    
    Write-Host "SUCCESS! Found $($quote.accommodations.Count) hotels"
    $quote.accommodations | Select-Object -First 3 | ForEach-Object {
        Write-Host "  - $($_.name): €$($_.combinations[0].price.amount)"
    }
} catch {
    Write-Host "ERROR: $($_.Exception.Message)"
    Write-Host "Status: $($_.Exception.Response.StatusCode.value__)"
    
    # Probeer response body te lezen
    $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
    $responseBody = $reader.ReadToEnd()
    Write-Host "Response: $responseBody"
}
```

---

## Mogelijke Uitkomsten

### ✅ Scenario 1: Het werkt!
```
SUCCESS! Found 15 hotels
  - Hotel Amsterdam: €150
  - Grand Hotel: €200
  - Budget Inn: €80
```

**Actie:** Deploy de Edge Function, het werkt!

---

### ❌ Scenario 2: 401 bij authenticatie
```
ERROR: The remote server returned an error: (401) Unauthorized.
```

**Probleem:** Username/password of micrositeId is fout

**Oplossing:**
1. Check je credentials bij Travel Compositor
2. Probeer andere micrositeId varianten:
   - `rondreis-planner`
   - `rondreis-planner.nl`
   - `www.rondreis-planner.nl`

---

### ❌ Scenario 3: 401 bij quote (na succesvolle auth)
```
Token: eyJhbGciOiJIUzUxMiJ9...
Expires in: 7200 seconds
ERROR: The remote server returned an error: (401) Unauthorized.
Response: {"message":"User not allowed to access here"}
```

**Probleem:** Je account heeft geen toegang tot de booking API

**Oplossing:**
1. Neem contact op met je Travel Compositor account manager
2. Vraag: "Kan ik toegang krijgen tot de booking/accommodations/quote endpoint?"
3. Vertel dat je alleen Quote (stap 1) nodig hebt voor content, geen daadwerkelijke bookings

---

### ❌ Scenario 4: 400 Bad Request
```
ERROR: The remote server returned an error: (400) Bad Request.
Response: {"error":"Invalid destination"}
```

**Probleem:** Destination code "AMS" bestaat niet voor jouw microsite

**Oplossing:** Haal eerst de beschikbare destinations op:

```powershell
$destinations = Invoke-RestMethod -Uri "https://online.travelcompositor.com/resources/destination/$micrositeId?lang=NL" `
    -Headers @{ "auth-token" = $token }

$destinations.destinations | Select-Object -First 10 | ForEach-Object {
    Write-Host "$($_.code) - $($_.name) ($($_.country))"
}
```

Gebruik een van deze codes in plaats van "AMS"

---

## Alternatieve Test: Zoek op Hotel Codes

Als destination niet werkt, probeer specifieke hotel codes:

```powershell
$quoteBody = @{
    checkIn = "2026-07-15"
    checkOut = "2026-07-18"
    distributions = @(
        @{
            persons = @(
                @{ age = 30 },
                @{ age = 30 }
            )
        }
    )
    language = "EN"
    sourceMarket = "ES"
    filter = @{
        bestCombinations = $true
        maxCombinations = 5
        includeOnRequestOptions = $true
    }
    timeout = 5000
    accommodations = @("1", "1000", "1003")  # Specifieke hotel IDs
} | ConvertTo-Json -Depth 10
```

---

## Debug Checklist

- [ ] Authenticatie werkt (krijg token terug)
- [ ] Token is geldig (niet verlopen)
- [ ] Quote endpoint geeft 401 → Geen booking API toegang
- [ ] Quote endpoint geeft 400 → Verkeerde destination/parameters
- [ ] Quote endpoint werkt → Success! Deploy de Edge Function

---

## Als het werkt: Update tc_microsites tabel

```sql
-- Voeg rondreis-planner toe met de EXACTE micrositeId die werkt
INSERT INTO tc_microsites (microsite_id, name, username, password, is_active)
VALUES (
  'rondreis-planner',  -- Of welke variant werkte
  'Rondreis Planner',
  'jouw_username',
  'jouw_password',
  true
);
```

Dan deploy:
```powershell
npx supabase functions deploy tc-hotel-quote --no-verify-jwt
```
