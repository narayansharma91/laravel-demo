FROM svikramjeet/php8.1

ARG BUILD
ENV BUILD=${BUILD}

ARG PORT
ENV PORT=${PORT}

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

RUN if [ "$BUILD" = "local" ] ; then ls -al ; else sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf ; fi

RUN chmod +x /var/www/html/db-migration.sh

ENTRYPOINT ["/var/www/html/db-migration.sh"]