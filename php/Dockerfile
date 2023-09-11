# https://www.twilio.com/blog/get-started-docker-symfony
# brew install php
# composer install
# symfony cli

FROM php:8.0-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    # postgresql
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

RUN echo 'max_execution_time = 120' >> /usr/local/etc/php/conf.d/docker-php-maxexectime.ini;
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memorylimit.ini;
RUN echo 'upload_max_filesize = 100M' >> /usr/local/etc/php/conf.d/docker-php-uploadmaxfilesize.ini;
RUN echo 'post_max_size = 100M' >> /usr/local/etc/php/conf.d/docker-php-postmaxsize.ini;



WORKDIR /var/www/symfony_docker

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Symfony CLI'yi doğrudan /usr/local/bin'e yükleyin
RUN curl -sS https://get.symfony.com/cli/installer | bash -s -- --install-dir=/usr/local/bin
RUN echo "short_open_tag = Off" >> /usr/local/etc/php/php.ini


RUN git config --global user.email "white.code.text@gmail.com" \ 
    && git config --global user.name "Sayzera"