FROM debian:wheezy

RUN apt-get upgrade -y
RUN apt-get update
RUN apt-get install -y wget ca-certificates

RUN wget -qO - http://www.dotdeb.org/dotdeb.gpg | apt-key add -
ADD docker-resources/dotdeb.list /etc/apt/sources.list.d/dotdeb.list

RUN apt-get update
RUN apt-get install -y php5 php5-imap php5-intl php5-curl php5-sqlite php5-mysql php5-tidy \
php-pear php5-dev pkg-config libzmq-dev

RUN printf "\n" | pecl install zmq-1.1.2
RUN echo "extension=zmq.so" > /etc/php5/cli/conf.d/20-zmq.ini
RUN echo "extension=zmq.so" > /etc/php5/apache2/conf.d/20-zmq.ini

RUN php -r "readfile('https://getcomposer.org/installer');" | php
RUN mv composer.phar /usr/local/bin/composer

RUN echo "date.timezone = America/Sao_Paulo" > /etc/php5/cli/conf.d/20-timezone.ini
RUN echo "date.timezone = America/Sao_Paulo" > /etc/php5/apache2/conf.d/20-timezone.ini
RUN echo "intl.default_locale = pt_BR" >> /etc/php5/mods-available/intl.ini


RUN a2enmod php5
RUN a2enmod rewrite
ADD docker-resources/apache-site-default.conf /etc/apache2/sites-available/default
