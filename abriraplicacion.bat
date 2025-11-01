@echo off
start "" "C:\xampp\xampp-control.exe"

timeout /t 3 /nobreak >nul

powershell -command "(New-Object -ComObject Shell.Application).MinimizeAll()"
start "" "http://localhost/savycg/sistema/"
