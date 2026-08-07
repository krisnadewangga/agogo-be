Set WshShell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")

' 1. MINTA HAK AKSES ADMINISTRATOR TERLEBIH DAHULU (Auto-Elevate)
If Not WScript.Arguments.Named.Exists("elevated") Then
  CreateObject("Shell.Application").ShellExecute "wscript.exe", """" & WScript.ScriptFullName & """ /elevated", "", "runas", 0
  WScript.Quit
End If

' 2. BARU TAMPILKAN INPUTBOX PORT (Hanya akan muncul 1x di sini)
dim ngrokPort
ngrokPort = InputBox("Masukkan Port untuk Ngrok / Laravel:", "Konfigurasi Port Server", "8000")

' Jika user menekan Cancel atau menyilang pop-up, batalkan eksekusi
If ngrokPort = "" Then
    WScript.Quit
End If

' 3. SET DIREKTORI & JALANKAN FILE .BAT DI BACKGROUND
scriptPath = fso.GetParentFolderName(WScript.ScriptFullName)
WshShell.CurrentDirectory = scriptPath

' Jalankan .bat dengan membawa parameter port
WshShell.Run "cmd.exe /c start-backend-server.bat " & ngrokPort, 0, False

Set WshShell = Nothing
Set fso = Nothing