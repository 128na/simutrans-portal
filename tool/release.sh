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
php -c /home/simutrans/www/php.ini /home/simutrans/bin/composer.phar install --optimize-autoloader --no-dev

echo ""
echo "|--------------------------------------------------------------------------"
echo "| update app version."
echo "|--------------------------------------------------------------------------"
git describe | php -c /home/simutrans/www/php.ini ./tool/updateAppVersion.php

echo ""
echo "|--------------------------------------------------------------------------"
echo "| optimize app."
echo "|--------------------------------------------------------------------------"
php -c /home/simutrans/www/php.ini artisan optimize:clear
php -c /home/simutrans/www/php.ini artisan optimize
php -c /home/simutrans/www/php.ini artisan view:cache
php -c /home/simutrans/www/php.ini artisan event:cache
php -c /home/simutrans/www/php.ini artisan route:clear

echo ""
echo "|--------------------------------------------------------------------------"
echo "| migration status."
echo "|--------------------------------------------------------------------------"
php -c /home/simutrans/www/php.ini artisan migrate:status
