Eu dockerizei um frontend em vue js e criei uma imagem para ele, segue o dockerfile e o docker compose assim como o env


# build stage
FROM node:lts-alpine as build-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
# Garantir que as variáveis de ambiente estejam disponíveis durante o build
ARG VITE_API_BASE_URL
ENV VITE_API_BASE_URL=${VITE_API_BASE_URL}
RUN npm run build

# production stage
FROM nginx:stable-alpine as production-stage
COPY --from=build-stage /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]




services:
  app:
    build: 
      context: .
      dockerfile: Dockerfile.production
      args:
        - VITE_API_BASE_URL=${VITE_API_BASE_URL}
    image: kaikesilva/controleobrasfrontend:latest
    env_file:
      - .env
    ports:
      - "127.0.0.1:3000:80"
    restart: unless-stopped
    labels:
      - traefik.enable=true
      - traefik.http.routers.controleobrasfrontend-http.rule=Host(`gestao.controleobras.online`)
      - traefik.http.routers.controleobrasfrontend-http.entrypoints=http
      - traefik.http.routers.controleobrasfrontend-http.middlewares=https-redirect
      - traefik.http.routers.controleobrasfrontend-https.rule=Host(`gestao.controleobras.online`)
      - traefik.http.routers.controleobrasfrontend-https.entrypoints=https
      - traefik.http.routers.controleobrasfrontend-https.tls=true
      - traefik.http.routers.controleobrasfrontend-https.tls.certresolver=le
      - traefik.http.routers.controleobrasfrontend-https.service=controleobrasfrontend-service
      - traefik.http.services.controleobrasfrontend-service.loadbalancer.server.port=3000

networks:
  default:
    name: traefik-public
    external: true

VITE_REVERB_APP_KEY=rpfnh21jtr3szlu5frah
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=6001
VITE_REVERB_SCHEME=ws
# VITE_REVERB_CLUSTER=mt1 # If you use a specific cluster
# VITE_API_BASE_URL=https://web.controleobras.online/api
VITE_API_BASE_URL=http://127.0.0.1:8080/api


como era apenas um unico service, eu criei a imagem e agora ao fazer push ele faz um push da nova imagem no docker hub e faz um pull dela na vps utilizando o seguinte git actions
# build-docker image and push to dockerhub
name: build_production_image

on:
  push:
    branches:
      - main
  pull_request:
    branches:
      - main

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
        # Dockerfile.production and docker-compose.production.yml should be used
      - name: Checkout
        uses: actions/checkout@v4
      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v3
      - name: Login to Docker Hub
        uses: docker/login-action@v2
        with:
          username: ${{ secrets.DOCKER_HUB_USERNAME }}
          password: ${{ secrets.DOCKER_HUB_PASSWORD }}
      - name: Build and push Docker image
        uses: docker/build-push-action@v5
        with:
          push: true
          tags: kaikesilva/controleobrasfrontend:latest
          context: .
          file: ./Dockerfile.production

  deploy:
    needs: build  # Só roda depois que a imagem for publicada
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to VPS via SSH
        uses: appleboy/ssh-action@v1.0.0
        with:
          host: ${{ secrets.SSH_HOST }}
          username: ${{ secrets.SSH_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            docker pull kaikesilva/controleobrasfrontend:latest
            docker compose -f /home/deployuser/projects/controle_obras/controle_obras_frontend/docker-compose.production.yml up -d

gostaria de fazer o mesmo para o backend, porem o backend possui varios servicos como seria este fluxo para varios servicos?
segue o docker compose do backend


services:
  # =============================================================================
  # NGINX - Reverse Proxy e Servidor Web
  # =============================================================================
  nginx:
    image: nginx:alpine
    container_name: nginx-prod
    restart: always
    ports:
      - "8080:80"
    volumes:
      - ./docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf:ro
      - app_public:/var/www/public:ro
      - ./public/swagger:/var/www/public/swagger:ro
    depends_on:
      - php
    networks:
      - app-network
    healthcheck:
      test: ["CMD", "wget", "--quiet", "--tries=1", "--spider", "http://localhost/"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 30s

  # =============================================================================
  # PHP-FPM - Aplicação Laravel
  # =============================================================================
  php:
    build:
      context: .
      dockerfile: Dockerfile.production
      args:
        - USER_ID=${USER_ID:-1000}
        - GROUP_ID=${GROUP_ID:-1000}
    image: ${APP_NAME:-laravel}-app:latest
    container_name: php-app-prod
    restart: always
    env_file:
      - .env.production
    environment:
      - CONTAINER_ROLE=app
    volumes:
      - app_public:/var/www/public
      - app_storage:/var/www/storage
      - app_bootstrap:/var/www/bootstrap/cache
    depends_on:
      - db
      - redis
    networks:
      - app-network
    deploy:
      resources:
        limits:
          memory: 512M
          cpus: '0.5'
        reservations:
          memory: 256M
          cpus: '0.25'
    security_opt:
      - no-new-privileges:true
    healthcheck:
      test: ["CMD", "php", "-v"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 60s

  # =============================================================================
  # WEBSOCKET - Laravel Reverb
  # =============================================================================
  websocket:
    build:
      context: .
      dockerfile: Dockerfile.production
      args:
        - USER_ID=${USER_ID:-1000}
        - GROUP_ID=${GROUP_ID:-1000}
    image: ${APP_NAME:-laravel}-websocket:latest
    container_name: websocket-prod
    restart: always
    env_file:
      - .env.production
    environment:
      - CONTAINER_ROLE=websocket
    ports:
      - "6001:6001"
    volumes:
      - app_storage:/var/www/storage
      - app_bootstrap:/var/www/bootstrap/cache
    depends_on:
      - db
      - redis
    networks:
      - app-network
    deploy:
      resources:
        limits:
          memory: 256M
          cpus: '0.25'
        reservations:
          memory: 128M
          cpus: '0.1'
    security_opt:
      - no-new-privileges:true
    healthcheck:
      test: ["CMD", "netcat", "-z", "localhost", "6001"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 60s

  # =============================================================================
  # QUEUE WORKER - Processamento de Filas
  # =============================================================================
  queue:
    build:
      context: .
      dockerfile: Dockerfile.production
      args:
        - USER_ID=${USER_ID:-1000}
        - GROUP_ID=${GROUP_ID:-1000}
    image: ${APP_NAME:-laravel}-queue:latest
    container_name: queue-prod
    restart: always
    env_file:
      - .env.production
    environment:
      - CONTAINER_ROLE=queue
    volumes:
      - app_storage:/var/www/storage
      - app_bootstrap:/var/www/bootstrap/cache
    depends_on:
      - db
      - redis
      - php
    networks:
      - app-network
    deploy:
      resources:
        limits:
          memory: 512M
          cpus: '0.5'
        reservations:
          memory: 256M
          cpus: '0.25'
    security_opt:
      - no-new-privileges:true
    healthcheck:
      test: ["CMD", "pgrep", "-f", "queue:work"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 90s

  # =============================================================================
  # MYSQL DATABASE
  # =============================================================================
  db:
    image: mysql:8.0
    container_name: mysql-prod
    restart: always
    environment:
      - MYSQL_DATABASE=${DB_DATABASE}
      - MYSQL_ROOT_PASSWORD=secret
    volumes:
      - db_data:/var/lib/mysql
    networks:
      - app-network
    command: >
      --default-authentication-plugin=mysql_native_password
      --bind-address=0.0.0.0
      --innodb-buffer-pool-size=256M
      --max-connections=100
    deploy:
      resources:
        limits:
          memory: 512M
          cpus: '0.5'
        reservations:
          memory: 256M
          cpus: '0.25'
    security_opt:
      - no-new-privileges:true
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-u", "root", "-psecret"]
      interval: 10s
      timeout: 5s
      retries: 5
      start_period: 30s

  # =============================================================================
  # REDIS - Cache e Sessões
  # =============================================================================
  redis:
    image: redis:7-alpine
    container_name: redis-prod
    restart: always
    environment:
      - REDIS_PASSWORD=secret
    volumes:
      - redis_data:/data
    networks:
      - app-network
    command: ["redis-server", "--requirepass", "secret", "--maxmemory", "128mb", "--maxmemory-policy", "allkeys-lru"]
    deploy:
      resources:
        limits:
          memory: 256M
          cpus: '0.25'
        reservations:
          memory: 128M
          cpus: '0.1'
    security_opt:
      - no-new-privileges:true
    healthcheck:
      test: ["CMD", "redis-cli", "-a", "secret", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5
      start_period: 30s

  # =============================================================================
  # SCHEDULER - Laravel Cron Jobs
  # =============================================================================
  scheduler:
    build:
      context: .
      dockerfile: Dockerfile.production
      args:
        - USER_ID=${USER_ID:-1000}
        - GROUP_ID=${GROUP_ID:-1000}
    image: ${APP_NAME:-laravel}-scheduler:latest
    container_name: scheduler-prod
    restart: always
    env_file:
      - .env.production
    environment:
      - CONTAINER_ROLE=scheduler
    volumes:
      - app_storage:/var/www/storage
      - app_bootstrap:/var/www/bootstrap/cache
    depends_on:
      - db
      - redis
      - php
    networks:
      - app-network
    deploy:
      resources:
        limits:
          memory: 256M
          cpus: '0.25'
        reservations:
          memory: 128M
          cpus: '0.1'
    security_opt:
      - no-new-privileges:true

# =============================================================================
# VOLUMES - Dados Persistentes
# =============================================================================
volumes:
  db_data:
    driver: local
  redis_data:
    driver: local
  app_storage:
    driver: local
  app_bootstrap:
    driver: local
  app_public:
    driver: local

# =============================================================================
# NETWORKS
# =============================================================================
networks:
  app-network:
    driver: bridge