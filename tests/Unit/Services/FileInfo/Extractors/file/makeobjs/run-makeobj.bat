@echo off
cd /d %~dp0

echo ========================================
echo Simutrans Pak Test Files Generator
echo ========================================
echo.

REM Copy source files
echo Copying source files...
copy /Y ..\test.dat test.dat >nul
copy /Y ..\test.png test.png >nul
copy /Y ..\test_transparent.png test_transparent.png >nul

echo.
echo Generating test pak files from unified test.dat...
echo.

REM makeobj 60.8 (default output name)
echo [1/5] makeobj-60.8
makeobj-60.8.exe pak128 ..\ test.dat

REM makeobj 60
echo [2/5] makeobj-60
makeobj-60.exe pak128 ..\test-60.pak test.dat

REM makeobj 55.4
echo [3/5] makeobj-55.4
makeobj-55.4.exe pak128 ..\test-55.4.pak test.dat

REM makeobj 50
echo [4/5] makeobj-50
makeobj-50.exe pak128 ..\test-50.pak test.dat

REM makeobj 48
echo [5/5] makeobj-48
makeobj-48.exe pak128 ..\test-48.pak test.dat

REM Cleanup
echo.
echo Cleaning up...
del test.dat test.png test_transparent.png

echo.
echo ========================================
echo All pak files generated successfully!
echo ========================================
echo.
echo Generated files:
echo   - way.test_1.pak (makeobj 60.8, way object)
echo   - way.test_transparent_1.pak (makeobj 60.8, way object with transparency)
echo   - vehicle.TestTruck.pak (makeobj 60.8, vehicle object)
echo   - test-48.pak (makeobj 48, all objects)
echo   - test-50.pak (makeobj 50, all objects)
echo   - test-55.4.pak (makeobj 55.4, all objects)
echo   - test-60.pak (makeobj 60, all objects)
echo.
echo Note: makeobj 60.8 creates separate .pak files per object
echo       makeobj 48-60 combine all objects into one .pak file
echo.
