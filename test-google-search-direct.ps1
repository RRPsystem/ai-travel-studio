# Test Google Custom Search API directly
$apiKey = "AIzaSyBMU9gf066k0rV-7ZePspwviPvOHYZuxOY"
$cseId = "9421bfea3ca6c47df"
$query = "weather Cape Verde"

$url = "https://www.googleapis.com/customsearch/v1?key=$apiKey&cx=$cseId&q=$([uri]::EscapeDataString($query))&num=3"

Write-Host "Testing Google Custom Search API..." -ForegroundColor Cyan
Write-Host "Query: $query" -ForegroundColor Yellow

try {
    $response = Invoke-RestMethod -Uri $url -Method Get
    Write-Host "`n✅ SUCCESS!" -ForegroundColor Green
    Write-Host "Found $($response.items.Count) results:" -ForegroundColor Green
    
    foreach ($item in $response.items) {
        Write-Host "`n📄 $($item.title)" -ForegroundColor White
        Write-Host "   $($item.snippet)" -ForegroundColor Gray
        Write-Host "   $($item.link)" -ForegroundColor Blue
    }
} catch {
    Write-Host "`n❌ ERROR!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    if ($_.ErrorDetails.Message) {
        $errorJson = $_.ErrorDetails.Message | ConvertFrom-Json
        Write-Host "`nDetails:" -ForegroundColor Yellow
        Write-Host ($errorJson | ConvertTo-Json -Depth 5) -ForegroundColor Gray
    }
}
