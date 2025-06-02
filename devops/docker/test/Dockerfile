FROM php:8.4-cli-alpine

# Install dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    icu-dev \
    && docker-php-ext-install intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Default command
CMD ["/bin/bash"]
