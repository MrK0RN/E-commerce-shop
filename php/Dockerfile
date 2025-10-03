FROM php:8.1-apache

COPY custom-php.ini /usr/local/etc/php/conf.d/custom.ini

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    pdo_pgsql \
    pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Включаем модули Apache
RUN a2enmod rewrite headers

# Создаем необходимые директории
RUN mkdir -p /var/www/html/storage
RUN mkdir -p /var/www/html/data \
    && mkdir -p /var/www/html/mail/quotes \
    && chown -R www-data:www-data /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/mail \
    && chmod 755 /var/www/html/data \
    && chmod 755 /var/www/html/mail/quotes

# Копируем файлы проекта
COPY . /var/www/html/

# Базовая конфигурация Apache
RUN echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog \${APACHE_LOG_DIR}/error.log\n\
    CustomLog \${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Устанавливаем правильные права
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/mail

WORKDIR /var/www/html
EXPOSE 8000
CMD ["apache2-foreground"]