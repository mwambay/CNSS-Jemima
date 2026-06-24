Commande actuelle pour désactiver tout de suite:
Disable-ScheduledTask -TaskName "Jemima CNSS Mail Scheduler"
Commande actuelle pour réactiver:
Enable-ScheduledTask -TaskName "Jemima CNSS Mail Scheduler"
Et pour forcer une exécution immédiate:
Start-ScheduledTask -TaskName "Jemima CNSS Mail Scheduler"