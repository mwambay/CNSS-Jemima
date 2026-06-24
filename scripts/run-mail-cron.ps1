$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$logDir = Join-Path $projectRoot 'storage\logs'
$logFile = Join-Path $logDir 'mail-cron.log'

if (-not (Test-Path $logDir)) {
    New-Item -ItemType Directory -Path $logDir | Out-Null
}

Set-Location $projectRoot

$startedAt = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
Add-Content -Encoding UTF8 -Path $logFile -Value "[$startedAt] schedule:run started"

php artisan schedule:run 2>&1 | ForEach-Object {
    Add-Content -Encoding UTF8 -Path $logFile -Value $_
}
$exitCode = $LASTEXITCODE

$finishedAt = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
Add-Content -Encoding UTF8 -Path $logFile -Value "[$finishedAt] schedule:run finished with exit code $exitCode"

exit $exitCode
