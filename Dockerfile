# Use an official PHP runtime as a parent image
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libev-dev \
    libevent-dev \
    libjansson-dev \
    libjemalloc-dev \
    libc-ares-dev \
    autoconf \
    automake \
    libtool \
    make \
    g++

# Install pecl/http extension
RUN pecl install http

# Enable PHP extension
RUN echo "extension=http.so" > /usr/local/etc/php/conf.d/docker-php-ext-http.ini

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Set permissions (if needed)
# RUN chown -R www-data:www-data /var/www

# Expose port 9000 (PHP-FPM default)
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
