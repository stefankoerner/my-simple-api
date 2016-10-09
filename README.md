Docker
======
docker build -t my-simple-api .
docker run -p 4202:4202 my-simple-api

Api Doc (Docker)
================
google-chrome chrome 127.0.0.1:4202/apidoc/

Api Doc (localhost)
===================
npm install
npm run apidoc
php -S 127.0.0.1:4202 -t src
google-chrome 127.0.0.1:4202/apidoc/
