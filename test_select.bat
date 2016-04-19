@echo off
for /L %%i in (1,1,1000) do (cls && echo Iteration: %%i/1000 && echo. && C:\wamp\bin\apache\apache2.4.17\bin\ab -n 500 -c 50 -l 740project.lc/random_select.php && timeout /t 2 /nobreak >nul)
pause
