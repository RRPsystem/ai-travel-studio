# Test Travel Compositor Authentication
# Dit script test verschillende microsite ID formaten om te vinden welke werkt

$ErrorActionPreference = "Stop"

# Configuratie
$TC_API_BASE = "https://online.travelcompositor.com/resources"
$USERNAME = "jouw_username_hier"  # VERVANG DIT
$PASSWORD = "jouw_password_hier"  # VERVANG DIT
$BASE_MICROSITE = "rondreis-planner"

Write-Host "=== Travel Compositor API Test ===" -ForegroundColor Cyan
Write-Host ""

# Test verschillende microsite ID varianten
$variants = @(
    $BASE_MICROSITE,
    "rondreis-planner.nl",
    "www.rondreis-planner.nl",
    "https://rondreis-planner.nl",
    "https://www.rondreis-planner.nl"
)

$results = @()

foreach ($msId in $variants) {
    Write-Host "Testing microsite ID: '$msId'" -ForegroundColor Yellow
    
    try {
        # Step 1: Authenticate
        $authBody = @{
            username = $USERNAME
            password = $PASSWORD
            micrositeId = $msId
        } | ConvertTo-Json
        
        $authResponse = Invoke-RestMethod -Uri "$TC_API_BASE/authentication/authenticate" `
            -Method POST `
            -Headers @{
                "Content-Type" = "application/json"
                "Accept-Encoding" = "gzip"
            } `
            -Body $authBody `
            -ErrorAction Stop
        
        if ($authResponse.token) {
            Write-Host "  ✓ Auth SUCCESS - Token received (${($authResponse.token.Length)} chars)" -ForegroundColor Green
            
            # Step 2: Try booking quote
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
                $quoteResponse = Invoke-RestMethod -Uri "$TC_API_BASE/booking/accommodations/quote" `
                    -Method POST `
                    -Headers @{
                        "Content-Type" = "application/json"
                        "auth-token" = $authResponse.token
                        "Accept-Encoding" = "gzip"
                    } `
                    -Body $quoteBody `
                    -ErrorAction Stop
                
                $hotelCount = $quoteResponse.accommodations.Count
                Write-Host "  ✓✓ QUOTE SUCCESS - Found $hotelCount hotels!" -ForegroundColor Green
                
                $results += [PSCustomObject]@{
                    MicrositeId = $msId
                    AuthStatus = "SUCCESS"
                    QuoteStatus = "SUCCESS"
                    HotelCount = $hotelCount
                }
                
            } catch {
                $statusCode = $_.Exception.Response.StatusCode.value__
                Write-Host "  ✗ Quote FAILED - HTTP $statusCode" -ForegroundColor Red
                
                $results += [PSCustomObject]@{
                    MicrositeId = $msId
                    AuthStatus = "SUCCESS"
                    QuoteStatus = "FAILED ($statusCode)"
                    HotelCount = 0
                }
            }
            
        } else {
            Write-Host "  ✗ Auth FAILED - No token in response" -ForegroundColor Red
            
            $results += [PSCustomObject]@{
                MicrositeId = $msId
                AuthStatus = "FAILED (No token)"
                QuoteStatus = "N/A"
                HotelCount = 0
            }
        }
        
    } catch {
        $statusCode = $_.Exception.Response.StatusCode.value__
        Write-Host "  ✗ Auth FAILED - HTTP $statusCode" -ForegroundColor Red
        
        $results += [PSCustomObject]@{
            MicrositeId = $msId
            AuthStatus = "FAILED ($statusCode)"
            QuoteStatus = "N/A"
            HotelCount = 0
        }
    }
    
    Write-Host ""
}

# Summary
Write-Host "=== RESULTS SUMMARY ===" -ForegroundColor Cyan
$results | Format-Table -AutoSize

# Find working variant
$working = $results | Where-Object { $_.QuoteStatus -like "SUCCESS*" }
if ($working) {
    Write-Host ""
    Write-Host "✓✓✓ WORKING MICROSITE ID: '$($working.MicrositeId)'" -ForegroundColor Green
    Write-Host "Use this exact value in your tc_microsites table!" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "✗ No working microsite ID found" -ForegroundColor Red
    Write-Host "Possible issues:" -ForegroundColor Yellow
    Write-Host "  1. Wrong username/password" -ForegroundColor Yellow
    Write-Host "  2. Account doesn't have booking API access" -ForegroundColor Yellow
    Write-Host "  3. Need different destination code (tried 'AMS')" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Contact Travel Compositor support with:" -ForegroundColor Yellow
    Write-Host "  - Your username: $USERNAME" -ForegroundColor Yellow
    Write-Host "  - Request: Enable booking API access for accommodations" -ForegroundColor Yellow
}
