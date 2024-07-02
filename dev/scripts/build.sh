#!/usr/bin/env bash

RELEASE_VERSION=$(cat gtm-consent-mode-banner.php | grep 'Version:' | awk -F' ' '{print $3}')

docker-compose run --rm -T php-cli <<INPUT
composer install
composer run check

rm -rf vendor

composer install --no-dev --optimize-autoloader

rm -rf dist/*
mkdir -p dist/gtm-consent-mode-banner

cp gtm-consent-mode-banner.php dist/gtm-consent-mode-banner/
cp readme.txt dist/gtm-consent-mode-banner/
cp -R src dist/gtm-consent-mode-banner/src
cp -R vendor dist/gtm-consent-mode-banner/vendor

cd dist && zip -r gtm-consent-mode-banner-$RELEASE_VERSION.zip gtm-consent-mode-banner

INPUT
