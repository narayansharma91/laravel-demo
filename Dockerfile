FROM php:8.2.0-apache

RUN apt-get update

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#Install zip+icu dev libs, wget, git
RUN apt-get install libzip-dev zip libicu-dev libpng-dev wget git -y

#Install PHP extensions zip and intl (intl requires to be configured)
RUN docker-php-ext-install zip && docker-php-ext-configure intl && docker-php-ext-install intl exif gd

#PostgreSQL
RUN apt-get update
RUN apt-get install libpq-dev -y

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && docker-php-ext-install pdo_pgsql pgsql

RUN a2enmod rewrite

WORKDIR /var/www/html

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

## ---------------------------------------
##          INSTALL SSL BEGINS
## ---------------------------------------

# Prepare fake SSL certificate
RUN apt-get install -y ssl-cert
RUN openssl req -new -newkey rsa:4096 -days 3650 -nodes -x509 -subj  "/C=UK/ST=EN/L=LN/O=FNL/CN=127.0.0.1" -keyout ./docker-ssl.key -out ./docker-ssl.pem -outform PEM
RUN mv docker-ssl.pem /etc/ssl/certs/ssl-cert-snakeoil.pem
RUN mv docker-ssl.key /etc/ssl/private/ssl-cert-snakeoil.key

# Setup Apache2 mod_ssl
RUN a2enmod ssl
# Setup Apache2 HTTPS env
RUN a2ensite default-ssl.conf

## ---------------------------------------
##          INSTALL SSL ENDS
## ---------------------------------------




## ---------------------------------------
##      INSTALL NODE JS [18.x] BEGINS
## ---------------------------------------

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs


ARG BUILD
ENV BUILD=${BUILD}


RUN service apache2 restart

COPY . /var/www/html

RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN a2enmod rewrite

RUN service apache2 restart

RUN if [ "$BUILD" = "local" ] ; then cp .env.example .env ; else ls -al ; fi

RUN if [ "$BUILD" = "local" ] ; then cp .env.example .env.testing ; else ls -al ; fi

RUN if [ "$BUILD" = "local" ] ; then ls -al ; else composer install --no-dev -n --prefer-dist ; fi
RUN if [ "$BUILD" = "local" ] ; then ls -al ; else chmod -R 0777 public storage bootstrap ; fi

RUN chmod +x /var/www/html/db-migration.sh

ENTRYPOINT ["/var/www/html/db-migration.sh"]