@echo off
setlocal EnableDelayedExpansion

:: 0. TANGKAP PORT DARI VBSCRIPT (%1)
set "TARGET_PORT=%1"
if "!TARGET_PORT!"=="" set "TARGET_PORT=8000"

:: 1. CEK HAK AKSES ADMINISTRATOR
openfiles >nul 2>&1
if %errorlevel% neq 0 (
    echo =======================================================
    echo HARAP JALANKAN SKRIP INI SEBAGAI ADMINISTRATOR!
    echo Klik kanan file .bat ini -^> Run as administrator.
    echo =======================================================
    pause
    exit /b
)

echo =======================================================
echo MEMERIKSA PRASYARAT SISTEM (GIT, NODE, PHP, COMPOSER, NGROK)
echo =======================================================
echo.

:: 2. CEK GIT
echo [1/5] Memeriksa Git...
git --version >nul 2>&1
if %errorlevel% == 0 (
    echo     -^> Git sudah terinstall.
) else (
    echo     -^> Git tidak ditemukan. Menginstall Git...
    set "GIT_INSTALLER=Git-2.53.0-64-bit.exe"
    if exist "%~dp0!GIT_INSTALLER!" (
        "%~dp0!GIT_INSTALLER!" /VERYSILENT /NORESTART /SP-
        echo     -^> Instalasi Git selesai.
    ) else (
        echo     [!] File !GIT_INSTALLER! tidak ditemukan di folder ini!
    )
)

:: 3. CEK NODE.JS
echo [2/5] Memeriksa Node.js...
node -v >nul 2>&1
if %errorlevel% == 0 (
    echo     -^> Node.js sudah terinstall.
) else (
    echo     -^> Node.js tidak ditemukan. Menginstall via Winget...
    winget install --id OpenJS.NodeJS --source winget --accept-package-agreements --accept-source-agreements --silent
    echo     -^> Instalasi Node.js selesai.
)

:: 4. CEK PHP
echo [3/5] Memeriksa PHP...
php -v >nul 2>&1
if %errorlevel% == 0 (
    echo     -^> PHP sudah terinstall.
) else (
    echo     -^> PHP tidak ditemukan. Menginstall via Winget...
    winget install --id PHP.PHP.8.3 --source winget --accept-package-agreements --accept-source-agreements --silent
    echo     -^> Instalasi PHP selesai.
)

:: 5. CEK COMPOSER
echo [4/5] Memeriksa Composer...
where composer >nul 2>&1
if %errorlevel% == 0 (
    echo     -^> Composer sudah terinstall.
) else (
    echo     -^> Composer tidak ditemukan. Mengunduh installer Composer...
    powershell -Command "Invoke-WebRequest -Uri 'https://getcomposer.org/Composer-Setup.exe' -OutFile '%~dp0Composer-Setup.exe'"
    echo     -^> Menjalankan installer Composer...
    start /wait %~dp0Composer-Setup.exe /VERYSILENT
    if exist "%~dp0Composer-Setup.exe" del "%~dp0Composer-Setup.exe"
    echo     -^> Instalasi Composer selesai!
)

:: 6. CEK NGROK (URL DIPERBARUI)
echo [5/5] Memeriksa Ngrok...
where ngrok >nul 2>&1
if %errorlevel% == 0 (
    echo     -^> Ngrok sudah terinstall.
) else (
    if exist "%~dp0ngrok.exe" (
        echo     -^> ngrok.exe ditemukan di folder lokal.
    ) else (
        echo     -^> Ngrok tidak ditemukan. Mengunduh Ngrok...
        powershell -Command "Invoke-WebRequest -Uri 'https://bin.equinox.io/c/bNyA16gRnB/ngrok-v3-stable-windows-amd64.zip' -OutFile '%~dp0ngrok.zip' -ErrorAction SilentlyContinue"
        if not exist "%~dp0ngrok.zip" (
            echo     -^> Download via Direct Link gagal, menginstall via Winget...
            winget install --id Ngrok.Ngrok --source winget --accept-package-agreements --accept-source-agreements --silent
        ) else (
            powershell -Command "Expand-Archive -Path '%~dp0ngrok.zip' -DestinationPath '%~dp0' -Force"
            if exist "%~dp0ngrok.zip" del "%~dp0ngrok.zip"
        )
        echo     -^> Unduh/Instalasi Ngrok selesai.
    )
)

echo.
echo =======================================================
echo Memulai Script Backend via Git Bash di Port !TARGET_PORT!...
echo =======================================================

set "GHOST_BASH=C:\Program Files\Git\bin\bash.exe"
set "SCRIPT_SH=%~dp0start-backend-server.sh"

:: Ambil path Parent Directory (agogo-be)
for /f "tokens=*" %%a in ('powershell -Command "(Get-Item '%~dp0..').FullName.Replace('\', '/').Replace('C:', '/c')"') do set "TARGET_DIR=%%a"

if exist "%GHOST_BASH%" (
    if exist "%SCRIPT_SH%" (
        "%GHOST_BASH%" -c "cd '!TARGET_DIR!' && ./run-program/start-backend-server.sh !TARGET_PORT!"
    ) else (
        echo [ERROR] File "start-backend-server.sh" tidak ditemukan di folder ini!
    )
) else (
    echo [ERROR] Git Bash tidak ditemukan di "C:\Program Files\Git\bin\bash.exe"
)