FROM devopsfnl/image:php-8.2-np

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install --no-progress
RUN npm install
RUN npm run build
