#!/bin/sh
cd `dirname $0`
cd ../

echo "|--------------------------------------------------------------------------"
echo "| current git information."
echo "|--------------------------------------------------------------------------"
git status
echo ""
echo "--------------"
git branch
echo ""
echo "--------------"
git log|head

echo ""
echo "|--------------------------------------------------------------------------"
echo "| execute git pull."
echo "|--------------------------------------------------------------------------"
git pull

echo ""
echo "|--------------------------------------------------------------------------"
echo "| updated git information."
echo "|--------------------------------------------------------------------------"
git log|head

echo ""
echo "|--------------------------------------------------------------------------"
echo "| update dependencies."
echo "|--------------------------------------------------------------------------"
php /home/simutrans/bin/composer.phar install --optimize-autoloader --no-dev

echo ""
echo "|--------------------------------------------------------------------------"
echo "| update app version."
echo "|--------------------------------------------------------------------------"
git describe | php ./tool/updateAppVersion.php

echo ""
echo "|--------------------------------------------------------------------------"
echo "| optimize app."
echo "|--------------------------------------------------------------------------"
php artisan cache:clear
php artisan optimize

echo ""
echo "|--------------------------------------------------------------------------"
echo "| migration status."
echo "|--------------------------------------------------------------------------"
php artisan migrate:status
