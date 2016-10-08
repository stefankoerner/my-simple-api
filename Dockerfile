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

# install node 6
RUN curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash - && \
	apt-get -y install \
	nodejs

# install php and postgresql
RUN apt-get update && apt-get -y install \
		postgresql \
		php5 \
		php5-pgsql \
		php5-intl

# install send_mail
RUN apt-get update && apt-get -y install \
		ssmtp && \
		echo "FromLineOverride=YES" >> /etc/ssmtp/ssmtp.conf && \
		echo 'sendmail_path = "/usr/sbin/ssmtp -t"' > /etc/php5/cli/conf.d/mail.ini

# build project
COPY ./ /www/my-simple-api
WORKDIR /www/my-simple-api
RUN wget http://getcomposer.org/composer.phar
RUN php composer.phar install

# create api doc
#RUN npm install && npm run apidoc

# cleanup
#RUN apt-get autoremove && apt-get autoclean && apt-get clean

EXPOSE 4202
ENTRYPOINT ["docker/run.sh"]
