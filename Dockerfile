FROM alpine:3.12.0

ARG TESTLINK_VERSION=1.9.20

RUN apk add --no-cache \
    php7-apache2 \
    php7-curl \
    php7-gd \
    php7-iconv \
    php7-json \
    php7-mbstring \
    php7-pgsql \
    php7-session && \
    rm -f /var/www/localhost/htdocs/index.html && \
    rm -f /var/log/apache2/access.log && ln -s /dev/stdout /var/log/apache2/access.log && \
    rm -f /var/log/apache2/error.log && ln -s /dev/stderr /var/log/apache2/error.log && \
    wget -O- https://github.com/TestLinkOpenSourceTRMS/testlink-code/archive/$TESTLINK_VERSION.tar.gz | tar -C /var/www/localhost/htdocs --strip 1 -xz && \
    mkdir -p /var/testlink/logs /var/testlink/upload_area && \
    chown -R apache:www-data /var/testlink/logs /var/testlink/upload_area /var/www/localhost/htdocs/gui/templates_c

VOLUME /var/testlink/logs /var/testlink/upload_area

COPY docker-entrypoint.sh /usr/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
