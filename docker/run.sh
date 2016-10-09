#!/bin/sh
set -e

ROOT="/www/my-simple-api"

# start postgresql server
service postgresql restart

# reset database
sudo -u postgres psql -f docker/my-simple-api.sql

# configure ssmtp
if [ "$SSMTP_MAILHUB" ]; then
    echo "mailhub=$SSMTP_MAILHUB" >> /etc/ssmtp/ssmtp.conf
fi;
if [ "$SSMTP_AUTH_USER" ]; then
    echo "AuthUser=$SSMTP_AUTH_USER" >> /etc/ssmtp/ssmtp.conf
fi;
if [ "$SSMTP_AUTH_PASS" ]; then
    echo "AuthPass=$SSMTP_AUTH_PASS" >> /etc/ssmtp/ssmtp.conf
fi;

# start php server
cd "$ROOT"
if [ "$FRONTEND_URL" ]; then
    echo "FRONTEND_URL=$FRONTEND_URL" >> src/.env
fi;
php -S 0.0.0.0:4202 -t src

