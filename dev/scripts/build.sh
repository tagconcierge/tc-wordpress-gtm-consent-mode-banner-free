#!/usr/bin/env bash

docker-compose run --rm -T php-cli <<INPUT
composer install --no-dev

rm -rf dist/
mkdir -p dist/gtm-consent-mode-banner

cp gtm-consent-mode-banner.php dist/gtm-consent-mode-banner/
cp readme.txt dist/gtm-consent-mode-banner/
cp -R src dist/gtm-consent-mode-banner/src
cp -R vendor dist/gtm-consent-mode-banner/vendor
cp -R composer.* dist/gtm-consent-mode-banner/

cd dist && zip -r gtm-consent-mode-banner.zip gtm-consent-mode-banner

INPUT
