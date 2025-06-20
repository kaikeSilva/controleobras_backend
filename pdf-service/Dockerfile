# ---------- Stage 1 – instala dependências sem dev ----------
    FROM node:20-alpine AS deps
    WORKDIR /app
    
    COPY package*.json ./
    RUN --mount=type=cache,target=/root/.npm \
        npm ci --omit=dev            # sempre usa lock se existir
    
    # ---------- Stage 2 – runtime + Chromium ----------
    FROM node:20-alpine
    
    ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
        PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium \
        NODE_ENV=production
    
    # Chromium + libs + wget (para health-check)
    RUN echo "http://dl-cdn.alpinelinux.org/alpine/edge/main" >> /etc/apk/repositories \
     && apk add --no-cache \
          chromium \
          nss \
          freetype \
          ttf-freefont \
          wget \
     && addgroup -S pptr && adduser -S pptr -G pptr
    
    WORKDIR /app
    
    # Copia node_modules e código já com owner pptr
    COPY --from=deps --chown=pptr:pptr /app/node_modules ./node_modules
    COPY --chown=pptr:pptr src/ ./src
    
    USER pptr
    EXPOSE 3000
    CMD ["node", "src/server.js"]
    