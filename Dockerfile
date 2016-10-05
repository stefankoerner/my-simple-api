#dockerfile
FROM ubuntu:14.04

ENV DEBIAN_FRONTEND noninteractive

# common packages
RUN apt-get update && apt-get -y install \
		software-properties-common \
		build-essential \
		curl \
		git \
		rsync \
		zip \
		wget \
		python \
		vim

# install php and postgresql
RUN apt-get update && apt-get -y install \
		postgresql \
		php5 \
		php5-pgsql \
		php5-intl

# build project
COPY ./ /www/my-simple-api
WORKDIR /www/my-simple-api
RUN wget http://getcomposer.org/composer.phar
RUN php composer.phar install

EXPOSE 4202
ENTRYPOINT ["docker/run.sh"]
