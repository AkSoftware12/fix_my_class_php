@echo off
echo.
echo === Fix My Class - Server Start ===
echo.

for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr /v "169.254"') do (
    set IP=%%a
    goto :found
)

:found
set IP=%IP: =%
echo Aapka IP: %IP%
echo Browser mein open karo: http://%IP%:8000
echo.
echo Server start ho raha hai... (band karne ke liye Ctrl+C)
echo.

cd /d "F:\PHP Projects\fix_my_class"
php artisan serve --host=0.0.0.0 --port=8000
pause
