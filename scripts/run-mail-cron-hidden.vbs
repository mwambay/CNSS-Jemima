Set shell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")

scriptDir = fso.GetParentFolderName(WScript.ScriptFullName)
runner = fso.BuildPath(scriptDir, "run-mail-cron.ps1")

command = "powershell.exe -NoProfile -ExecutionPolicy Bypass -File """ & runner & """"
shell.Run command, 0, True
