#!/bin/sh
set -e

ROOT="/www/my-simple-api"

cd "$ROOT"

php composer.phar start

