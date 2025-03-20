FROM ubuntu:latest

# Add ondrej/php PPA
RUN apt-get update && apt-get install -y software-properties-common && \
    add-apt-repository ppa:ondrej/php && apt-get update

# Install dependencies
RUN apt-get update && apt-get install -y \
    apache2 \
    php8.2 \
    libapache2-mod-php8.2 \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-xml \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-zip \
    php8.2-bcmath \
    php8.2-intl \
    php8.2-gd \
    php8.2-sqlite3 \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    curl \
    git \
    unzip \
    gosu \
    libsqlite3-dev \
    mysql-client \
    vim \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Add Listen 8080 to ports.conf
RUN echo "Listen 8080" >> /etc/apache2/ports.conf

# Set up the Laravel user and group
RUN if ! getent group laravel; then groupadd -g 1000 laravel || groupadd -g 1001 laravel; fi && \
    if ! id -u laravel; then useradd -u 1000 -g laravel -m laravel || useradd -u 1001 -g laravel -m laravel; fi

# Create necessary Laravel directories
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Set permissions
RUN chown -R laravel:laravel /var/www/html && \
    find /var/www/html -type f -exec chmod 664 {} \; && \
    find /var/www/html -type d -exec chmod 775 {} \; && \
    chown -R laravel:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy the application code
COPY . /var/www/html

# Set permissions again after copying the code
RUN chown -R laravel:laravel /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set the working directory
WORKDIR /var/www/html

# Copy the Apache configuration file
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable the new site
RUN a2ensite 000-default.conf

# Expose the port
EXPOSE 8080

# Start Apache in the foreground
CMD ["apache2ctl", "-D", "FOREGROUND"]
