$username = "apiuser"
$password = "apiuser123"
$micrositeId = "rondreis-planner"

Write-Host "=== Step 1: Authenticate ===" -ForegroundColor Cyan

$authBody = @{
    username = $username
    password = $password
    micrositeId = $micrositeId
} | ConvertTo-Json

try {
    $auth = Invoke-RestMethod -Uri "https://online.travelcompositor.com/resources/authentication/authenticate" -Method POST -Headers @{"Content-Type" = "application/json"; "Accept-Encoding" = "gzip"} -Body $authBody
    
    Write-Host "SUCCESS Auth OK" -ForegroundColor Green
    Write-Host "Token: $($auth.token.Substring(0, 50))..." -ForegroundColor Gray
    Write-Host "Expires: $($auth.expirationInSeconds)s" -ForegroundColor Gray
    
    Write-Host ""
    Write-Host "=== Step 2: Try Hotel Quote ===" -ForegroundColor Cyan
    
    $quoteBody = @{
        checkIn = "2026-07-15"
        checkOut = "2026-07-18"
        distributions = @(@{ persons = @(@{ age = 30 }, @{ age = 30 }) })
        language = "EN"
        sourceMarket = "ES"
        filter = @{ bestCombinations = $true; maxCombinations = 5; includeOnRequestOptions = $true }
        timeout = 5000
        destinationId = "AMS"
    } | ConvertTo-Json -Depth 10
    
    try {
        $quote = Invoke-RestMethod -Uri "https://online.travelcompositor.com/resources/booking/accommodations/quote" -Method POST -Headers @{"Content-Type" = "application/json"; "auth-token" = $auth.token; "Accept-Encoding" = "gzip"} -Body $quoteBody
        
        Write-Host "SUCCESS QUOTE OK!" -ForegroundColor Green
        Write-Host "Hotels found: $($quote.accommodations.Count)" -ForegroundColor Gray
        
        if ($quote.accommodations.Count -gt 0) {
            Write-Host ""
            Write-Host "First 3 hotels:" -ForegroundColor Yellow
            $quote.accommodations | Select-Object -First 3 | ForEach-Object {
                $price = $_.combinations[0].price.amount
                Write-Host "  - $($_.name): EUR $price" -ForegroundColor White
            }
        }
        
    } catch {
        Write-Host "FAILED Quote endpoint returned error" -ForegroundColor Red
        Write-Host "Status: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
        
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response: $responseBody" -ForegroundColor Red
        
        Write-Host ""
        Write-Host "=== DIAGNOSE ===" -ForegroundColor Yellow
        Write-Host "Your account does NOT have access to the booking API." -ForegroundColor Yellow
        Write-Host ""
        Write-Host "Solution:" -ForegroundColor Cyan
        Write-Host "1. Contact Travel Compositor support" -ForegroundColor White
        Write-Host "2. Ask for access to /booking/accommodations/quote endpoint" -ForegroundColor White
        Write-Host "3. Tell them you only need Quote step 1 for content, no actual bookings" -ForegroundColor White
    }
    
} catch {
    Write-Host "FAILED Authentication failed" -ForegroundColor Red
    Write-Host "Status: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    
    $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
    $responseBody = $reader.ReadToEnd()
    Write-Host "Response: $responseBody" -ForegroundColor Red
    
    Write-Host ""
    Write-Host "Check:" -ForegroundColor Yellow
    Write-Host "1. Username: $username" -ForegroundColor White
    Write-Host "2. Password: Check tc_microsites table in Supabase" -ForegroundColor White
    Write-Host "3. MicrositeId: $micrositeId" -ForegroundColor White
}
