@echo off
cd /d %~dp0

makeobj-60.8.exe pak128 ..\test-60.8.pak ..\test.dat
makeobj-60.8.exe pak128 ..\test_transparent-60.8.pak ..\test_transparent.dat

makeobj-60.exe pak128 ..\test-60.pak ..\test.dat
makeobj-60.exe pak128 ..\test_transparent-60.pak ..\test_transparent.dat

makeobj-55.4.exe pak128 ..\test-55.4.pak ..\test.dat
makeobj-55.4.exe pak128 ..\test_transparent-55.4.pak ..\test_transparent.dat

makeobj-50.exe pak128 ..\test-50.pak ..\test.dat
makeobj-50.exe pak128 ..\test_transparent-50.pak ..\test_transparent.dat

makeobj-48.exe pak128 ..\test-48.pak ..\test.dat
makeobj-48.exe pak128 ..\test_transparent-48.pak ..\test_transparent.dat
