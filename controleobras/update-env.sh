#!/bin/bash

# Update Laravel .env file with Docker environment settings
sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=pgsql/' backend/.env
sed -i 's/DB_HOST=127.0.0.1/DB_HOST=db/' backend/.env
sed -i 's/DB_PORT=3306/DB_PORT=5432/' backend/.env
sed -i 's/DB_DATABASE=laravel/DB_DATABASE=laravel/' backend/.env
sed -i 's/DB_USERNAME=root/DB_USERNAME=postgres/' backend/.env
sed -i 's/DB_PASSWORD=/DB_PASSWORD=secret/' backend/.env

# Update Redis configuration
sed -i 's/REDIS_HOST=127.0.0.1/REDIS_HOST=redis/' backend/.env
sed -i 's/REDIS_PASSWORD=null/REDIS_PASSWORD=null/' backend/.env
sed -i 's/REDIS_PORT=6379/REDIS_PORT=6379/' backend/.env

# Update cache and session drivers to use Redis
sed -i 's/CACHE_DRIVER=file/CACHE_DRIVER=redis/' backend/.env
sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=redis/' backend/.env

echo "Environment file updated successfully!"
