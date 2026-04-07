@echo off

:: Use 'call' to ensure the script continues after the git command
call git add .
git commit -a -m "pull"
git pull origin master

echo Sync complete.
pause