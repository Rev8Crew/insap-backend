FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
        cron \
        rsyslog \
        curl \
        wget \
        git \
        jq \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libxslt-dev \
        libicu-dev \
        libmcrypt-dev \
        libxml2-dev \
        libsodium-dev \
        zlib1g-dev \
        libpcre3-dev \
        libcurl4-openssl-dev \
        pkg-config \
        libssl-dev \
        supervisor \
    && a2enmod \
        rewrite \
    && ACCEPT_EULA=Y \
        DEBIAN_FRONTEND=noninteractive \
        apt-get install --yes \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure \
        gd \
    && docker-php-ext-install \
        gd \
        mysqli \
        pdo_mysql \
        sockets \
        zip \
        mbstring \
        xml \
    && pecl install \
        mongodb \
        xdebug \
    && docker-php-ext-enable mongodb \
        xdebug

# Add the user UID:1000, GID:1000, home at /app
RUN groupadd -r app -g 1000 && useradd -u 1000 -r -g app -m -d /app -s /sbin/nologin -c "App user" app && \
    chmod 755 /var/www/html

#Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin && \
        echo "alias composer='composer'" >> /root/.bashrc && \
        composer

#Supervisord
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
#Php.ini
ADD php.ini /usr/local/etc/php

RUN chmod 755 /var/www/html

USER app
WORKDIR /var/www/html

USER root
COPY default.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
EXPOSE 80
