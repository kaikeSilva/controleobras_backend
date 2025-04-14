# Laravel + Vue.js Docker Application

This project is a fullstack application with Laravel backend, Vue.js frontend, PostgreSQL database, and Redis cache, all dockerized for easy development.

## Project Structure

```
/
├── backend/          # Laravel application
├── frontend/         # Vue.js application
├── docker-compose.yml
├── init-laravel.sh   # Script to initialize Laravel project
└── README.md
```

## Prerequisites

- Docker
- Docker Compose

## Setup Instructions

### 1. Clone the repository

```bash
git clone <repository-url>
cd <repository-directory>
```

### 2. Start the Docker containers

```bash
docker compose up -d
```

Note: Use `docker compose` (without the hyphen) as specified for your Docker version.

### 3. Initialize the Laravel project

```bash
# Enter the Laravel container
docker compose exec app bash

# Inside the container, run the initialization script
bash /var/www/init-laravel.sh

# Exit the container
exit
```

### 4. Access the applications

- Laravel Backend: http://localhost:8081
- Vue.js Frontend: http://localhost:5173

## Development

### Laravel Backend

- The Laravel application is mounted as a volume, so any changes you make to the code will be reflected immediately.
- To run migrations or other artisan commands:
  ```bash
  docker compose exec app php artisan migrate
  ```

### Vue.js Frontend

- The Vue.js application has Hot Module Replacement (HMR) enabled, so changes will be reflected immediately.
- To install additional npm packages:
  ```bash
  docker compose exec frontend npm install <package-name>
  ```

## Common Issues and Solutions

### Laravel Storage Permissions

The Dockerfile is configured to set the correct permissions for the Laravel storage directory. If you encounter permission issues, you can run:

```bash
docker compose exec app chmod -R 777 /var/www/storage
```

### Vue.js Hot Reload Not Working

If hot reload is not working, check that:
1. The `vite.config.ts` file has the correct configuration
2. Your browser can access the WebSocket connection (no firewall blocking)

## Database

- PostgreSQL is accessible on port 5432
- Default credentials:
  - Database: laravel
  - Username: postgres
  - Password: secret

## Redis

- Redis is accessible on port 6379
- No authentication is required by default
