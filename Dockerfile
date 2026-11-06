# Base image with PHP and Apache
FROM php:8.2-apache

# Install system dependencies and PHP extension requirements
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mysqli

# Enable Apache Mod Rewrite for clean routing if needed
RUN a2enmod rewrite

# Set working directory inside container
WORKDIR /var/www/html

# Copy application files into the Apache document root
COPY . /var/www/html/

# Set correct file permissions for web server and uploads directory
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/uploads

# Expose standard HTTP port
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]