REM build script initializer
REM author: Andrew Ritchie
@echo off
set /p cloud="Is this a Cloud installation? Y for yes, anything else for no: "
if %cloud%==y (set cloud=Y)
if %cloud%==Y (
  goto cloud
)
:bitnamiprompt
set /p bitnami="Are you using Bitnami WAMPStack? (Y/N) "
if %bitnami%==y (set bitnami=Y)
if %bitnami%==n (set bitnami=N)
set valid=0
if %bitnami%==Y (set valid=1) else if %bitnami%==N (set valid=1) else (echo Invalid input)
if %valid%==0 goto bitnamiprompt
if %bitnami%==Y (
  set /p bitnamidir="Input Bitnami WAMPStack directory (i.e. C:\Bitnami\wampstack-7.3.5-0) "
  set htdocs=%bitnamidir%\apache2\htdocs
  set phploc=%bitnamidir%\php\php.exe
  echo Bitnami Directory: %bitnamidir%
) else (
  set /p phploc="Input PHP executable file location php.exe: "
  set /p htdocs="Input htdocs folder location: "
)
set /p sqluser="Input MySQL Admin User Name: "
set /p sqlpass="Input MySQL Admin Password: "
set /p sqldb="Input new MySQL Database Name:"
set /p sqlloc="Input SQL url (i.e. localhost): "
set /p adminuser="Input Scehduling OnDemand Admin Email Address: "
set /p adminpass="Input Scehduling OnDemand Admin Password: "
set /p unittests="Include Unit Tests? Y for yes, anything else for no: "
echo Current location: %cd%
echo php.exe location: %phploc%
echo htdocs location: %htdocs%
echo The following actions will be performed:
echo All files in htdocs will be removed and replaced with Scheduling OnDemand
echo The database %sqldb% will be created.
echo A new MySQL user, "soduser", will be created with a random password.
echo The MySQL user, "soduser", will be granted Read and Write access to %sqldb%
if %unittests%==y (set unittests=Y)
if %unittests%==Y (
  echo The database testdb will be created.
  echo The MySQL user, "soduser", will be granted Read and Write access to testdb
)
set /p confirmation="Type 'confirm' to confirm: "
if %confirmation%==confirm (
    echo Starting installation.
    call %phploc% .\setup.php
) else (
    echo Cancelled installation of Scheduling OnDemand
)
goto end
:cloud
if exist dist rd /s /q dist
if exist dist rd /s /q dist
mkdir dist\images
copy images\*.* dist\images
mkdir dist\misc
copy misc\*.* dist\misc
mkdir dist\models
copy models\*.* dist\models
mkdir dist\services
copy services\*.* dist\services
mkdir dist\config
copy configplaceholder.php dist\config\config.php
copy *.php dist
copy *.css dist
echo Read README.TXT to complete installation
:end