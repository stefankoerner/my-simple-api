FROM ubuntu:14.04

RUN echo "1.565.1" > .lts-version-number

RUN apt-get update && apt-get install -y wget git curl zip vim
RUN apt-get update && apt-get install -y apache2 php5 php5-pgsql
RUN apt-get update && apt-get install -y php5-intl imagemagick

RUN usermod -U www-data && chsh -s /bin/bash www-data

RUN echo 'ServerName ${SERVER_NAME}' >> /etc/apache2/conf-enabled/servername.conf

COPY enable-var-www-html-htaccess.conf /etc/apache2/conf-enabled/
COPY run_apache.sh /var/www/
RUN a2enmod rewrite 


#USER www-data

#VOLUME ["/var/www/html", "/var/log/apache2" ]
ENV SERVER_NAME docker-apache-php


# for main web interface:
EXPOSE 80

WORKDIR /var/www/html


CMD ["/var/www/run_apache.sh"]
