$ErrorActionPreference = 'Stop'

$taskName = 'Jemima CNSS Mail Scheduler'
$projectRoot = Split-Path -Parent $PSScriptRoot
$runner = Join-Path $projectRoot 'scripts\run-mail-cron-hidden.vbs'

if (-not (Test-Path $runner)) {
    throw "Runner introuvable: $runner"
}

$action = New-ScheduledTaskAction `
    -Execute 'wscript.exe' `
    -Argument "`"$runner`""

$trigger = New-ScheduledTaskTrigger `
    -Once `
    -At (Get-Date).Date `
    -RepetitionInterval (New-TimeSpan -Minutes 1) `
    -RepetitionDuration (New-TimeSpan -Days 3650)

$settings = New-ScheduledTaskSettingsSet `
    -StartWhenAvailable `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries

Register-ScheduledTask `
    -TaskName $taskName `
    -Action $action `
    -Trigger $trigger `
    -Settings $settings `
    -Description 'Runs Laravel schedule:run for CNSS email reminders.' `
    -Force | Out-Null

Write-Host "Tache planifiee installee: $taskName"
Write-Host "Le scheduler Laravel sera appele chaque minute. Les rappels mail partent selon routes/console.php."
