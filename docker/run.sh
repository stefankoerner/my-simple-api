#!/bin/sh
set -e

ROOT="/www/my-simple-api"

# start postgresql server
service postgresql start

# reset database
sudo -u postgres psql -f docker/my-simple-api.sql

# start php server
cd "$ROOT"
php -S 0.0.0.0:4202 -t src

