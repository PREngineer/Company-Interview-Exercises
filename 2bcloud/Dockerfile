# Base Image is Alpine
FROM alpine:3.15

# Maintainer information and description
LABEL maintainer="Jorge Pabón <pianistapr@hotmail.com>" description="A hello world application for 2bcloud."

# Setup Apache and PHP 7.4, also create the directory that will hold our application files /app
RUN apk --no-cache --update \
    add apache2 \
    apache2-ssl \
    curl \
    php7-apache2 \
    php7-bcmath \
    php7-bz2 \
    php7-calendar \
    php7-common \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-gd \
    php7-iconv \
    php7-mbstring \
    php7-mysqli \
    php7-mysqlnd \
    php7-openssl \
    php7-pdo \
    php7-pdo_dblib \
    php7-pdo_mysql \
    php7-pdo_odbc \
    php7-pdo_pgsql \
    php7-pdo_sqlite \
    php7-phar \
    php7-session \
    php7-xml \
    tzdata \
    && mkdir /app

# Copy our application to the /app directory
COPY ./app /app
RUN chmod -R 777 /app

# Expose our web ports
EXPOSE 80

# Add the entrypoint script
ADD ./entrypoint.sh /
RUN ["chmod", "+x", "/entrypoint.sh"]

# Execute the entrypoint script
ENTRYPOINT ["/entrypoint.sh"]