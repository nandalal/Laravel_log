<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Step 1: Create a Dockerfile for PHP

Path: /laravel-project/docker/php/Dockerfile

dockerfile

# Use an official PHP image as a parent image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy application
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]

## Step 2: Create a Nginx Configuration File

Path: /laravel-project/docker/nginx/default.conf

nginx

server {
    listen 80;

    root /var/www/html/public;
    index index.php index.html;

    server_name localhost;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}

## Step 3: Create docker-compose.yml

Path: /laravel-project/docker/docker-compose.yml

yaml

version: '3.8'

services:
  app:
    build:
      context: ../
      dockerfile: docker/php/Dockerfile
    container_name: laravel_app
    volumes:
      - ../:/var/www/html
    networks:
      - laravel

  nginx:
    image: nginx:latest
    container_name: laravel_nginx
    ports:
      - "8000:80"
    volumes:
      - ../:/var/www/html
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - laravel

  mysql:
    image: mysql:8.0
    container_name: laravel_mysql
    restart: always
    environment:
      MYSQL_DATABASE: laravel
      MYSQL_USER: laravel_user
      MYSQL_PASSWORD: laravel_password
      MYSQL_ROOT_PASSWORD: root_password
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"
    networks:
      - laravel

networks:
  laravel:

volumes:
  mysql_data:
  
## Step 4: Update .env for Database Configuration

Update your .env file to connect to the MySQL container:

env

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password

## Step 5: Build and Start Docker Containers

Run the following commands to build and start your containers:

bash

# Navigate to the Docker directory
cd /laravel-project/docker

# Build and start containers
docker-compose up -d --build

## Step 6: Install Dependencies

Once the containers are running, access the app container and install Laravel dependencies:

bash

# Enter the app container
docker exec -it laravel_app bash

# Install Composer dependencies
composer install

# Exit the container
exit

## Step 7: Set Permissions

Set proper permissions for Laravel directories:

bash

docker exec -it laravel_app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

## Step 8: Run Migrations

Run Laravel migrations to set up the database:

bash

docker exec -it laravel_app bash -c "php artisan migrate"
Step 9: Access Your Application
Your Laravel application should now be accessible at http://localhost:8000.
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
