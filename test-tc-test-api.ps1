$username = "apiuser"
$password = "apiuser123"
$micrositeId = "rondreis-planner"

Write-Host "=== Testing PRODUCTION vs TEST API ===" -ForegroundColor Cyan
Write-Host ""

# Test both endpoints
$endpoints = @{
    "PRODUCTION" = "https://online.travelcompositor.com/resources"
    "TEST" = "https://test-api-accommodation.travelcompositor.com/resources"
}

foreach ($env in $endpoints.Keys) {
    $baseUrl = $endpoints[$env]
    
    Write-Host "Testing $env API: $baseUrl" -ForegroundColor Yellow
    
    try {
        # Step 1: Auth
        $authBody = @{
            username = $username
            password = $password
            micrositeId = $micrositeId
        } | ConvertTo-Json
        
        $auth = Invoke-RestMethod -Uri "$baseUrl/authentication/authenticate" -Method POST -Headers @{"Content-Type" = "application/json"; "Accept-Encoding" = "gzip"} -Body $authBody
        
        Write-Host "  Auth: SUCCESS" -ForegroundColor Green
        
        # Step 2: Quote
        $quoteBody = @{
            checkIn = "2026-07-15"
            checkOut = "2026-07-18"
            distributions = @(@{ persons = @(@{ age = 30 }, @{ age = 30 }) })
            language = "EN"
            sourceMarket = "ES"
            filter = @{ bestCombinations = $true; maxCombinations = 5; includeOnRequestOptions = $true }
            timeout = 5000
            destinationId = "MAD"
        } | ConvertTo-Json -Depth 10
        
        try {
            $quote = Invoke-RestMethod -Uri "$baseUrl/booking/accommodations/quote" -Method POST -Headers @{"Content-Type" = "application/json"; "auth-token" = $auth.token; "Accept-Encoding" = "gzip"} -Body $quoteBody
            
            Write-Host "  Quote: SUCCESS - Found $($quote.accommodations.Count) hotels" -ForegroundColor Green
            
            if ($quote.accommodations.Count -gt 0) {
                $hotel = $quote.accommodations[0]
                $price = $hotel.combinations[0].price.amount
                Write-Host "  Example: $($hotel.name) - EUR $price" -ForegroundColor White
            }
            
        } catch {
            Write-Host "  Quote: FAILED - $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
        }
        
    } catch {
        Write-Host "  Auth: FAILED - $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    }
    
    Write-Host ""
}

Write-Host "=== CONCLUSION ===" -ForegroundColor Cyan
Write-Host "If TEST API works but PRODUCTION fails:" -ForegroundColor Yellow
Write-Host "  -> You have test access but need production API access" -ForegroundColor White
Write-Host "If both fail:" -ForegroundColor Yellow
Write-Host "  -> Contact Travel Compositor for booking API access" -ForegroundColor White
