# Use the latest alpine version
FROM alpine:latest

LABEL mantainer="Jorge Pabón <pianistapr@hotmail.com>" description="An app that returns JSON data from Azure."

# Install Apache2 and PHP8.  Also make the /app directory in the container.
RUN apk --no-cache --update \
    add apache2 \
    apache2-ssl \
    curl \
    php81-apache2 \
    php81-bcmath \
    php81-bz2 \
    php81-calendar \
    php81-common \
    php81-ctype \
    php81-curl \
    php81-dom \
    php81-gd \
    php81-iconv \
    php81-mbstring \
    php81-mysqli \
    php81-mysqlnd \
    php81-openssl \
    php81-pdo \
    php81-pdo_dblib \
    php81-pdo_mysql \
    php81-pdo_odbc \
    php81-pdo_pgsql \
    php81-pdo_sqlite \
    php81-phar \
    php81-session \
    php81-xml \
    && mkdir /app

# Set our Work Directory to be /app
WORKDIR /app

# Copy our application to the /app directory, make it accessible to all users
COPY ./App /app
RUN chmod -R 777 /app

# Add the entrypoint script to set Apache and PHP and to start the Apache web server, make it executable inside the container
ADD ./entrypoint.sh /
RUN ["chmod", "+x", "/entrypoint.sh"]

# Expose web port
EXPOSE 80

# Execute the entrypoint script
ENTRYPOINT ["/entrypoint.sh"]