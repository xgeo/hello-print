FROM php:7.4-cli

COPY ./sql/init.sql /docker-entrypoint-initdb.d/
COPY . /var/www/hello-print
WORKDIR /var/www/hello-print

ENV BUILD_DEPS \
        build-essential \
        git \
        libpq-dev \
        python-dev \
        wget \
        libsasl2-dev \
        libssl-dev \
        zlib1g-dev \
        libicu-dev \
        curl \
        zip \
        unzip \
        libzip-dev

RUN apt-get update \
    && apt-get install -y --no-install-recommends ${BUILD_DEPS} \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip \
    && sed -i -e 's/;extension=pgsql/extension=pgsql/' /usr/local/etc/php/conf.d/docker-php-ext-pgsql.ini \
    && sed -i -e 's/;extension=pdo_pgsql/extension=pdo_pgsql/' /usr/local/etc/php/conf.d/docker-php-ext-pdo_pgsql.ini \
    && sed -i -e 's/;extension=pdo_pgsql/extension=sodium/' /usr/local/etc/php/conf.d/docker-php-ext-sodium.ini

RUN cd /tmp \
    && git clone \
    --depth 1 \
    https://github.com/edenhill/librdkafka.git \
    && cd librdkafka \
    && ./configure \
    && make \
    && make install \
    && pecl install rdkafka \
    && pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port=9006" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && docker-php-ext-enable rdkafka xdebug \
    && rm -rf /tmp/librdkafka

RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/local/bin/ --filename=composer
RUN php -r "unlink('composer-setup.php');"
RUN cd /var/www/hello-print && composer install

CMD ["php"]