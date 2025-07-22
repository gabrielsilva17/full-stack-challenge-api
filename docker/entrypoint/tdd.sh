#!/bin/bash
. /usr/local/bin/porcentage.sh

if ! [ -f "database/database.sqlite" ]; then
    rm -rf database/database.sqlite
fi

touch database/database.sqlite

if ! [ -f ".env" ]; then
    cp .env.testing .env
fi

echo "[ ${PORCENTAGE_0} ] Starting test with PHP Unit"
echo "[ ${PORCENTAGE_10} ] Install depedencies"
composer install --ignore-platform-reqs --verbose
echo "[ ${PORCENTAGE_50} ] DB Migration and Seed"
php artisan migrate --seed
echo "[ ${PORCENTAGE_80} ] Run PHP Unit and generate coverage"
phpdbg -qrr bin/phpunit --log-junit coverage/phpunit.report.xml
sed -i -e 's|/var/www/||g' coverage/phpunit.coverage.xml
sed -i -e 's|/var/www/||g' coverage/phpunit.report.xml
echo "[ ${PORCENTAGE_100} ] End of test with PHP Unit"
