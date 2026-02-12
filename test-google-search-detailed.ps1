# Test Google Custom Search API with detailed error info
$apiKey = "AIzaSyBMU9gf066k0rV-7ZePspwviPvOHYZuxOY"
$cseId = "9421bfea3ca6c47df"
$query = "weather Cape Verde"

$url = "https://www.googleapis.com/customsearch/v1?key=$apiKey&cx=$cseId&q=$([uri]::EscapeDataString($query))&num=3"

Write-Host "Testing Google Custom Search API..." -ForegroundColor Cyan
Write-Host "URL: $url" -ForegroundColor Gray

try {
    $response = Invoke-WebRequest -Uri $url -Method Get -UseBasicParsing
    $json = $response.Content | ConvertFrom-Json
    Write-Host "`n✅ SUCCESS!" -ForegroundColor Green
    Write-Host "Found $($json.items.Count) results" -ForegroundColor Green
} catch {
    Write-Host "`n❌ ERROR!" -ForegroundColor Red
    Write-Host "Status: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    
    $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
    $errorBody = $reader.ReadToEnd()
    $reader.Close()
    
    Write-Host "`nError Response:" -ForegroundColor Yellow
    Write-Host $errorBody -ForegroundColor Gray
    
    try {
        $errorJson = $errorBody | ConvertFrom-Json
        Write-Host "`nParsed Error:" -ForegroundColor Yellow
        Write-Host "Message: $($errorJson.error.message)" -ForegroundColor Red
        Write-Host "Reason: $($errorJson.error.errors[0].reason)" -ForegroundColor Red
    } catch {
        Write-Host "Could not parse error JSON" -ForegroundColor Red
    }
}
