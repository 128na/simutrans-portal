@echo off
cd /d %~dp0

echo ========================================
echo Simutrans Pak Test Files Generator
echo ========================================
echo.

REM Copy source files
echo Copying source files...
copy /Y ..\test.dat test.dat >nul
copy /Y ..\test_transparent.dat test_transparent.dat >nul
copy /Y ..\vehicle.dat vehicle.dat >nul
copy /Y ..\test.png test.png >nul
copy /Y ..\test_transparent.png test_transparent.png >nul

echo.
echo Generating test pak files...
echo.

REM makeobj 60.8
echo [1/5] makeobj-60.8
makeobj-60.8.exe pak128 ..\ test.dat
makeobj-60.8.exe pak128 ..\ test_transparent.dat
makeobj-60.8.exe pak128 ..\ vehicle.dat

REM makeobj 60
echo [2/5] makeobj-60
makeobj-60.exe pak128 ..\test-60.pak test.dat
makeobj-60.exe pak128 ..\test_transparent-60.pak test_transparent.dat
makeobj-60.exe pak128 ..\vehicle-60-temp.pak vehicle.dat
if exist ..\vehicle-60-temp.pak (
    copy /Y ..\vehicle-60-temp.pak ..\vehicle.TestTruck-60.pak >nul
    del ..\vehicle-60-temp.pak
)

REM makeobj 55.4
echo [3/5] makeobj-55.4
makeobj-55.4.exe pak128 ..\test-55.4.pak test.dat
makeobj-55.4.exe pak128 ..\test_transparent-55.4.pak test_transparent.dat
makeobj-55.4.exe pak128 ..\vehicle-55.4-temp.pak vehicle.dat
if exist ..\vehicle-55.4-temp.pak (
    copy /Y ..\vehicle-55.4-temp.pak ..\vehicle.TestTruck-55.4.pak >nul
    del ..\vehicle-55.4-temp.pak
)

REM makeobj 50
echo [4/5] makeobj-50
makeobj-50.exe pak128 ..\test-50.pak test.dat
makeobj-50.exe pak128 ..\test_transparent-50.pak test_transparent.dat
makeobj-50.exe pak128 ..\vehicle-50-temp.pak vehicle.dat
if exist ..\vehicle-50-temp.pak (
    copy /Y ..\vehicle-50-temp.pak ..\vehicle.TestTruck-50.pak >nul
    del ..\vehicle-50-temp.pak
)

REM makeobj 48
echo [5/5] makeobj-48
makeobj-48.exe pak128 ..\test-48.pak test.dat
makeobj-48.exe pak128 ..\test_transparent-48.pak test_transparent.dat
makeobj-48.exe pak128 ..\vehicle-48-temp.pak vehicle.dat
if exist ..\vehicle-48-temp.pak (
    copy /Y ..\vehicle-48-temp.pak ..\vehicle.TestTruck-48.pak >nul
    del ..\vehicle-48-temp.pak
)

REM Cleanup
echo.
echo Cleaning up...
del test.dat test_transparent.dat vehicle.dat test.png test_transparent.png

echo.
echo ========================================
echo All pak files generated successfully!
echo ========================================
echo.
echo Generated files:
echo   - test-*.pak (way objects, 5 versions)
echo   - test_transparent-*.pak (way objects with transparency, 5 versions)
echo   - vehicle.TestTruck.pak (makeobj 60.8, vehicle object)
echo   - vehicle.TestTruck-*.pak (makeobj 48-60, vehicle objects)
echo.
