FROM php:7.4-apache

ARG GOLANG_VERSION=1.17.5

RUN apt-get update && apt-get install -y \
        cron \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libzip-dev \
        libonig-dev \
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
        python3 \
        python3-pip

RUN a2enmod \
        rewrite \
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
    && docker-php-ext-enable mongodb

# Go Lang
RUN wget https://go.dev/dl/go$GOLANG_VERSION.linux-amd64.tar.gz  \
    && tar -C /usr/local -xzf go$GOLANG_VERSION.linux-amd64.tar.gz

ENV PATH=$PATH:/usr/local/go/bin
ENV GOPATH=$HOME/.go

RUN go version

#Composer
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin && \
        echo "alias composer='composer'" >> /root/.bashrc &&  \
        composer

# CLEANUP
RUN apt-get autoremove -yqq --purge \
 && apt-get clean \
 && rm -rf \
    /var/lib/apt/lists/* \
    /tmp/* \
    /var/tmp/* \
    /usr/share/man \
    /usr/share/doc \
    /usr/share/doc-base

#Supervisord
RUN apt-get update && apt-get install -y supervisor
RUN mkdir -p /var/log/supervisor
RUN mkdir -p /var/www/html/storage/supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
#Php.ini
ADD php.ini /usr/local/etc/php

RUN chmod 755 /var/www/html

USER root
COPY default.conf /etc/apache2/sites-enabled/000-default.conf

RUN ln -s /usr/local/bin/php /usr/bin/php

#CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
CMD ["/usr/bin/supervisord"]
EXPOSE 80
