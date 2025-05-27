FROM dunglas/frankenphp:1.6-php8.2-bookworm

# Create a non-root user and set permissions
ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=$USER_UID

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    default-mysql-client \
    libjpeg-dev \
    libmagickwand-dev \
    libmcrypt-dev \
    libonig-dev \
    libpng-dev \
    libxslt1-dev \
    libzip-dev \
    sudo \
    unzip \
    vim \
    zip \
    openssh-client \
    git

# add additional extensions here:
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    gd \
    intl \
    zip \
    opcache

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy Xdebug configuration
COPY config/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

#install composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

# Add user and group, and set permissions for required folders
RUN groupadd --gid $USER_GID $USERNAME \
    && useradd -s /bin/bash --uid $USER_UID --gid $USER_GID -m $USERNAME \
    && chown -R $USER_UID:$USER_GID /app

# Grant additional capabilities if needed
RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
    && chown -R $USER_UID:$USER_GID /data/caddy /config/caddy

# Switch to the non-root user
USER $USERNAME

# Changing Workdir
WORKDIR /app
