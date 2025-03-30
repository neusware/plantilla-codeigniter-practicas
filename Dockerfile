FROM php:7.2.34-apache

# Instalación de paquetes adicionales y bibliotecas entorno php y apache
RUN apt-get update && apt-get install -y \
    apt-utils \
    curl \
    dnsutils \
    git \
    unzip \
    wget \
    zip \
    libcurl4-gnutls-dev \
    libmcrypt-dev \
    libtidy-dev \
    libbz2-dev \
    libxml2-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62 \
    libpng-dev \
    libssl-dev \
    libicu-dev \
    libc-client-dev \
    libkrb5-dev \
    jpegoptim \
    && docker-php-ext-install mysqli pdo_mysql \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/ \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install \
        bcmath \
        bz2 \
        calendar \
        curl \
        exif \
        ftp \
        gd \
        gettext \
        imap \
        intl \
        json \
        mbstring \
        opcache \
        pdo \
        pdo_mysql \
        shmop \
        soap \
        sockets \
        sysvmsg \
        sysvsem \
        sysvshm \
        tidy \
        wddx \
        zip

# Instalación de python
RUN apt-get install -y python3 python3-pip
# Instalación python3 y posiblemente scripts
RUN a2enmod rewrite headers \
    && echo "<VirtualHost *:80> \n \
        ServerAdmin webmaster@localhost \n \
        DocumentRoot /var/www/html \n \
        ErrorLog \${APACHE_LOG_DIR}/error.log \n \
        CustomLog \${APACHE_LOG_DIR}/access.log combined \n \
        <Directory /var/www/html> \n \
            Options Indexes FollowSymLinks MultiViews \n \
            AllowOverride All \n \
            Order allow,deny \n \
            allow from all \n \
            Require all granted \n \
        </Directory> \n \
    </VirtualHost>" > /etc/apache2/sites-available/000-default.conf \
    && service apache2 restart

# Comprobar la versión de Python 3
RUN python3 --version
