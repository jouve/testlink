FROM php:7.2.12-apache-stretch

RUN apt update && \
    apt install -y libpq5 libpq-dev && \
    docker-php-ext-install mysqli pgsql && \
    docker-php-ext-enable mysqli pgsql && \
    apt purge -y libpq-dev && \
    apt clean && \
    rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/testlink/logs /var/testlink/upload_area && \
    chown -R www-data:www-data . /var/testlink/logs /var/testlink/upload_area

USER www-data

ARG TESTLINK_VERSION=1.9.17
RUN curl -L https://github.com/TestLinkOpenSourceTRMS/testlink-code/archive/$TESTLINK_VERSION.tar.gz | tar --strip 1 -xz

COPY PHPMailerAutoload.php ./third_party/phpmailer/PHPMailerAutoload.php

USER root

RUN mkdir -p /usr/share/testlink && mv install /usr/share/testlink/install

COPY installNewDB.php /usr/share/testlink/install

VOLUME /var/testlink/logs /var/testlink/upload_area

COPY docker-entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT [ "/docker-entrypoint.sh" ]

CMD ["apache2-foreground"]
