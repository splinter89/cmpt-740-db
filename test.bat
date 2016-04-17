@echo off
for /L %%i in (1,1,1000) do (cls && echo Iteration: %%i/1000 && echo. && C:\wamp\bin\apache\apache2.4.17\bin\ab -n 100 -c 100 740project.lc/add_random.php && timeout /t 2 /nobreak >nul)
pause
