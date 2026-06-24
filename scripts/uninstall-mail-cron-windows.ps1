$ErrorActionPreference = 'Stop'

$taskName = 'Jemima CNSS Mail Scheduler'

if (Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue) {
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
    Write-Host "Tache planifiee supprimee: $taskName"
    exit 0
}

Write-Host "Aucune tache planifiee trouvee: $taskName"
