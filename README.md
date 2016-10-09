Docker
======
```
docker build -t my-simple-api .
docker run -d -p 4202:4202 my-simple-api
```

Mail Service
============
```
docker run -it --name my-simple-api -p 4202:4202 -e SSMTP_MAILHUB='smtp.gmail.com:587' -e SSMTP_AUTH_USER='someEmail' -e SSMTP_AUTH_PASS='somePassword' my-simple-api
```

Api Doc (Docker)
================
```
google-chrome 127.0.0.1:4202/apidoc/
```

Api Doc (localhost)
===================
```
npm install
npm run apidoc
php -S 127.0.0.1:4202 -t src
google-chrome 127.0.0.1:4202/apidoc/
```
