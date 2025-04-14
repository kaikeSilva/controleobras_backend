FROM node:18-alpine

# Set working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy environment files
COPY .env* ./

# Copy project files
COPY . .

# Set NODE_ENV to development by default
ENV NODE_ENV=development

# Expose port for Vue dev server
EXPOSE 5173

# Start development server
CMD ["npm", "run", "dev", "--", "--host"]
