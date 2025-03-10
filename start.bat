@echo off
echo Starting Product Label Generator...
cd /d %~dp0
php-bin\php.exe -S 0.0.0.0:8000 -t public
pause
