FROM php:7.4-cli as phpworker
COPY . /usr/src/hello-print
WORKDIR /usr/src/hello-print

ENV BUILD_DEPS \
        build-essential \
        git \
        libsasl2-dev \
        libssl-dev \
        python-minimal \
        zlib1g-dev
RUN apt-get update \
    && apt-get install -y --no-install-recommends ${BUILD_DEPS} \
    && cd /tmp \
    && git clone \
        --depth 1 \
        https://github.com/edenhill/librdkafka.git \
    && cd librdkafka \
    && ./configure \
    && make \
    && make install \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka \
    && rm -rf /tmp/librdkafka \
    && apt-get purge \
        -y --auto-remove \
        -o APT::AutoRemove::RecommendsImportant=false \
        ${BUILD_DEPS}

RUN apt-get update && apt-get install -y libmcrypt-dev \
&& docker-php-ext-install pdo pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer