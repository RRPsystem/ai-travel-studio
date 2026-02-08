# ============================================
# TravelC Reizen - Plugin Build Script
# Bouwt een correcte WordPress plugin zip
# Gebruik: Rechtermuisklik > Run with PowerShell
# ============================================

$ErrorActionPreference = "Stop"

$pluginName = "travelc-reizen"
$sourceDir  = Join-Path $PSScriptRoot "$pluginName\$pluginName"
$zipFile    = Join-Path $PSScriptRoot "$pluginName.zip"

# Controleer of bronmap bestaat
if (-not (Test-Path $sourceDir)) {
    Write-Host "FOUT: Bronmap niet gevonden: $sourceDir" -ForegroundColor Red
    Read-Host "Druk op Enter om af te sluiten"
    exit 1
}

# Controleer of travelc-reizen.php bestaat
$mainPhp = Join-Path $sourceDir "$pluginName.php"
if (-not (Test-Path $mainPhp)) {
    Write-Host "FOUT: Plugin bestand niet gevonden: $mainPhp" -ForegroundColor Red
    Read-Host "Druk op Enter om af te sluiten"
    exit 1
}

# Verwijder oude zip
if (Test-Path $zipFile) {
    Remove-Item $zipFile -Force
    Write-Host "Oude zip verwijderd" -ForegroundColor Yellow
}

# Bouw zip met forward slashes (WordPress vereist dit)
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$zip = [System.IO.Compression.ZipFile]::Open($zipFile, [System.IO.Compression.ZipArchiveMode]::Create)

$files = Get-ChildItem -Path $sourceDir -Recurse -File
$count = 0

foreach ($file in $files) {
    # Skip zip bestanden en temp bestanden
    if ($file.Extension -eq ".zip") { continue }
    if ($file.Name.StartsWith(".")) { continue }
    
    $relativePath = $file.FullName.Substring($sourceDir.Length).TrimStart('\')
    $entryName = "$pluginName/" + ($relativePath -replace '\\','/')
    
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
        $zip, $file.FullName, $entryName, 
        [System.IO.Compression.CompressionLevel]::Optimal
    ) | Out-Null
    
    $count++
    Write-Host "  + $entryName" -ForegroundColor Gray
}

$zip.Dispose()

# Verificatie
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host " ZIP AANGEMAAKT: $pluginName.zip" -ForegroundColor Green
Write-Host " Bestanden: $count" -ForegroundColor Green
Write-Host " Locatie: $zipFile" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "UPLOAD INSTRUCTIES:" -ForegroundColor Cyan
Write-Host "1. Ga naar WordPress > Plugins" -ForegroundColor White
Write-Host "2. Als '$pluginName' al bestaat: Deactiveer + Verwijder" -ForegroundColor White
Write-Host "3. Klik 'Nieuwe plugin' > 'Plugin uploaden'" -ForegroundColor White
Write-Host "4. Selecteer: $zipFile" -ForegroundColor White
Write-Host "5. Activeer de plugin" -ForegroundColor White
Write-Host ""
Read-Host "Druk op Enter om af te sluiten"
