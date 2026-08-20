@echo off
setlocal
cd /d "%~dp0"

if exist "C:\jdk-21\bin\java.exe" set "JAVA_HOME=C:\jdk-21"
if exist "C:\Program Files\Java\jdk-21\bin\java.exe" set "JAVA_HOME=C:\Program Files\Java\jdk-21"
if exist "C:\Program Files\Eclipse Adoptium\jdk-21*\bin\java.exe" for /d %%i in ("C:\Program Files\Eclipse Adoptium\jdk-21*") do set "JAVA_HOME=%%i"

echo.
echo Sunrise Dental Clinic
echo Folder: %CD%
echo JAVA_HOME: %JAVA_HOME%
echo Start MySQL in XAMPP first, then wait for http://localhost:8080/
echo.

call "%~dp0mvnw.cmd" -q compile exec:java
echo.
echo Server stopped.
pause
