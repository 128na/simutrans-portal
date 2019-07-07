#!/bin/sh
cd `dirname $0`
cd ..

echo "|--------------------------------------------------------------------------"
echo "| current git information"
echo "|--------------------------------------------------------------------------"
git status
echo "--------------"
git branch
echo "--------------"
git log|head

read -p "Hit enter: "
echo "|--------------------------------------------------------------------------"
echo "| updated git information"
echo "|--------------------------------------------------------------------------"

git pull
echo "--------------"
git log|head

read -p "Hit enter: "
echo "|--------------------------------------------------------------------------"
echo "| optimize app"
echo "|--------------------------------------------------------------------------"

php artisan config:cache
echo "--------------"
php artisan route:cache
echo "--------------"
echo "complete!"
