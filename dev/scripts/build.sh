#!/usr/bin/env bash

docker-compose run --rm -T php-cli <<INPUT
composer install --no-dev

rm -rf dist/
mkdir -p dist/gtm-cookies-free

cp gtm-cookies-free.php dist/gtm-cookies-free/
cp -R src dist/gtm-cookies-free/src
cp -R vendor dist/gtm-cookies-free/vendor
cp -R composer.* dist/gtm-cookies-free/

cd dist && zip -r gtm-cookies-free.zip gtm-cookies-free

INPUT
