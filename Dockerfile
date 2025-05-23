FROM php:8.4 AS php

# Instalar Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs
# Instalar dependências do sistema incluindo procps para o comando 'free' e netcat para verificação de portas
RUN apt-get update -y && apt-get install -y \
    unzip \
    libpq-dev \
    libcurl4-gnutls-dev \
    procps \
    netcat-openbsd \
    sudo \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_mysql bcmath pcntl

# Instalar Redis
RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# Criar usuário não-root
ARG USER_ID=1000
ARG GROUP_ID=1000
RUN groupadd -g ${GROUP_ID} appuser && \
    useradd -u ${USER_ID} -g appuser -m -s /bin/bash appuser && \
    echo 'appuser ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

# Configurar diretório de trabalho
WORKDIR /var/www/

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar código da aplicação
COPY . .

# Ajustar permissões
RUN chown -R appuser:appuser /var/www/

# Variável de ambiente para porta
ENV PORT=8000

# Dar permissão de execução para os scripts
RUN chmod +x docker/entrypoint.sh
RUN chmod +x docker/tests/run-tests.sh

# Mudar para usuário não-root
USER appuser

# Definir entrypoint
ENTRYPOINT ["docker/entrypoint.sh"]

FROM node:22-alpine AS node

# Install global packages as root first
RUN npm install --global cross-env

# Criar usuário não-root no container Node
ARG USER_ID=1000
ARG GROUP_ID=1000

# Create 'appuser' and 'appgroup' only if USER_ID/GROUP_ID are not the default 'node' (1000)
RUN if [ "$USER_ID" != "1000" ] || [ "$GROUP_ID" != "1000" ]; then \
      echo "Creating new user appuser ($USER_ID) and group appgroup ($GROUP_ID)" && \
      addgroup -g ${GROUP_ID} -S appgroup && \
      adduser -u ${USER_ID} -S appuser -G appgroup; \
    else \
      echo "Will use existing node user (UID $USER_ID) and group (GID $GROUP_ID)"; \
    fi

WORKDIR /var/www/

# Copy package.json and package-lock.json
COPY package.json package-lock.json* ./

# Ensure the WORKDIR and copied package files are owned by the target user
RUN chown -R ${USER_ID}:${GROUP_ID} /var/www/

USER ${USER_ID}

# Install dependencies, creating node_modules in the image layer owned by USER_ID
RUN npm install

# The rest of the application code (e.g., vite.config.js, resources/js, etc.)
# will be mapped via the bind mount in docker-compose.yaml.
# If we wanted a fully self-contained image for production, we'd COPY . . here.
