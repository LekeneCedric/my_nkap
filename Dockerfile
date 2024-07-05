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
    libnghttp2-dev \
    zlib1g-dev \
    libpcre3-dev \
    autoconf \
    automake \
    libtool \
    make \
    g++ \
    && docker-php-ext-install curl json

# Install pecl/http extension dependencies
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libevent-dev \
    libssl-dev \
    libpcre3-dev

# Install pecl/http extension
RUN pecl install raphf \
    && pecl install propro \
    && pecl install http

# Enable PHP extensions
RUN docker-php-ext-enable raphf propro http

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
