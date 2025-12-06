# Dockerfile Creation Guide

Complete guide for creating and understanding Dockerfiles for the PHP User Management Application.

## 📋 Table of Contents

1. [What is a Dockerfile?](#what-is-a-dockerfile)
2. [Our Dockerfile Explained](#our-dockerfile-explained)
3. [Creating Your First Dockerfile](#creating-your-first-dockerfile)
4. [Dockerfile Best Practices](#dockerfile-best-practices)
5. [Common Modifications](#common-modifications)
6. [Multi-Stage Builds](#multi-stage-builds)
7. [Testing Your Dockerfile](#testing-your-dockerfile)
8. [Troubleshooting](#troubleshooting)

---

## What is a Dockerfile?

A **Dockerfile** is a text file that contains instructions for building a Docker image. It's like a recipe that tells Docker how to create a containerized version of your application.

### Key Concepts

- **Image**: A read-only template used to create containers
- **Container**: A running instance of an image
- **Layer**: Each instruction in a Dockerfile creates a new layer
- **Base Image**: The starting point for your image (e.g., `php:8.2-apache`)

---

## Our Dockerfile Explained

Let's break down our current Dockerfile line by line:

```dockerfile
# Use official PHP Apache image
FROM php:8.2-apache
```

**What it does:** Sets the base image with PHP 8.2 and Apache web server pre-installed.

**Why:** Provides a ready-to-use PHP environment without manual installation.

---

```dockerfile
# Set working directory
WORKDIR /var/www/html
```

**What it does:** Sets the working directory inside the container to `/var/www/html` (Apache's default document root).

**Why:** All subsequent commands run from this directory, and files are copied here.

---

```dockerfile
# Copy application files
COPY index.php /var/www/html/
```

**What it does:** Copies `index.php` from your local machine to the container.

**Why:** Makes your application code available inside the container.

**Note:** The destination path `/var/www/html/` is the same as WORKDIR, so you could also write `COPY index.php .`

---

```dockerfile
# Enable Apache mod_rewrite (if needed in future)
RUN a2enmod rewrite
```

**What it does:** Enables Apache's rewrite module.

**Why:** Needed for URL rewriting (useful for clean URLs, though not currently used).

---

```dockerfile
# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
```

**What it does:** 
- Changes ownership to `www-data` (Apache's user)
- Sets permissions to 755 (read/execute for all, write for owner)

**Why:** Ensures Apache can read and serve your files correctly.

---

```dockerfile
# Expose port 80
EXPOSE 80
```

**What it does:** Documents that the container listens on port 80.

**Why:** Informs users which port to map when running the container.

**Note:** This doesn't actually open the port - it's documentation. The port is opened by Apache.

---

```dockerfile
# Start Apache server
CMD ["apache2-foreground"]
```

**What it does:** Sets the default command to start Apache in foreground mode.

**Why:** Keeps the container running and Apache serving your application.

---

## Creating Your First Dockerfile

### Step-by-Step Process

1. **Create a new file named `Dockerfile`** (no extension)

2. **Start with a base image:**
   ```dockerfile
   FROM php:8.2-apache
   ```

3. **Set working directory:**
   ```dockerfile
   WORKDIR /var/www/html
   ```

4. **Copy your files:**
   ```dockerfile
   COPY index.php .
   # Or copy multiple files
   COPY *.php .
   COPY css/ ./css/
   COPY js/ ./js/
   ```

5. **Install dependencies (if needed):**
   ```dockerfile
   RUN apt-get update && apt-get install -y \
       git \
       curl \
       && rm -rf /var/lib/apt/lists/*
   ```

6. **Configure the environment:**
   ```dockerfile
   RUN a2enmod rewrite
   RUN chown -R www-data:www-data /var/www/html
   ```

7. **Expose port:**
   ```dockerfile
   EXPOSE 80
   ```

8. **Set startup command:**
   ```dockerfile
   CMD ["apache2-foreground"]
   ```

### Complete Example

```dockerfile
# Base image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd

# Copy application files
COPY index.php .
COPY config/ ./config/

# Enable Apache modules
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Set environment variables
ENV APACHE_DOCUMENT_ROOT=/var/www/html
ENV PHP_INI_DIR=/usr/local/etc/php

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
```

---

## Dockerfile Best Practices

### 1. Use Specific Base Image Tags

❌ **Bad:**
```dockerfile
FROM php:latest
```

✅ **Good:**
```dockerfile
FROM php:8.2-apache
```

**Why:** Ensures consistent builds and avoids unexpected changes.

### 2. Order Instructions Efficiently

❌ **Bad:**
```dockerfile
COPY index.php .
RUN apt-get update
COPY config.php .
RUN apt-get install -y git
```

✅ **Good:**
```dockerfile
RUN apt-get update && apt-get install -y git && rm -rf /var/lib/apt/lists/*
COPY index.php .
COPY config.php .
```

**Why:** Docker caches layers. Put frequently changing files (your code) last.

### 3. Combine RUN Commands

❌ **Bad:**
```dockerfile
RUN apt-get update
RUN apt-get install -y git
RUN apt-get install -y curl
RUN rm -rf /var/lib/apt/lists/*
```

✅ **Good:**
```dockerfile
RUN apt-get update && apt-get install -y \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*
```

**Why:** Reduces number of layers and image size.

### 4. Use .dockerignore

Create a `.dockerignore` file:

```
.git
.gitignore
README.md
*.md
.vscode
.idea
node_modules
.env
```

**Why:** Excludes unnecessary files from the build context, making builds faster.

### 5. Don't Run Services in Build

❌ **Bad:**
```dockerfile
RUN service apache2 start
```

✅ **Good:**
```dockerfile
CMD ["apache2-foreground"]
```

**Why:** Services should start when the container runs, not during build.

### 6. Use Non-Root User (Production)

```dockerfile
RUN useradd -m -u 1000 appuser
USER appuser
```

**Why:** Improves security by not running as root.

### 7. Add Health Checks

```dockerfile
HEALTHCHECK --interval=30s --timeout=3s \
  CMD curl -f http://localhost/ || exit 1
```

**Why:** Allows Docker to monitor container health.

---

## Common Modifications

### Add PHP Extensions

```dockerfile
# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql
```

### Add Composer

```dockerfile
# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader
```

### Add Environment Variables

```dockerfile
# Set environment variables
ENV APP_ENV=production
ENV DEBUG=false
ENV PHP_MEMORY_LIMIT=256M

# Use in PHP
RUN echo "memory_limit = ${PHP_MEMORY_LIMIT}" >> /usr/local/etc/php/php.ini
```

### Copy Multiple Files/Folders

```dockerfile
# Copy all PHP files
COPY *.php .

# Copy entire directory
COPY src/ ./src/

# Copy with specific permissions
COPY --chown=www-data:www-data index.php .
```

### Add Custom Apache Configuration

```dockerfile
# Copy Apache config
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Enable site
RUN a2ensite 000-default.conf
```

### Install Additional Software

```dockerfile
# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Python
RUN apt-get update && apt-get install -y python3
```

---

## Multi-Stage Builds

Multi-stage builds help create smaller final images by using intermediate stages.

### Example: Build and Runtime Stages

```dockerfile
# Stage 1: Build stage
FROM php:8.2-apache AS builder

WORKDIR /build

# Install build dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Runtime stage
FROM php:8.2-apache

WORKDIR /var/www/html

# Copy only necessary files from builder
COPY --from=builder /build/vendor ./vendor
COPY index.php .

# Final configuration
RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
```

**Benefits:**
- Smaller final image (no build tools)
- Better security (fewer packages)
- Faster deployments

---

## Testing Your Dockerfile

### 1. Build the Image

```bash
# Basic build
docker build -t php-user-app .

# Build with no cache
docker build --no-cache -t php-user-app .

# Build with progress output
docker build --progress=plain -t php-user-app .
```

### 2. Run the Container

```bash
# Basic run
docker run -d -p 8080:80 --name test-container php-user-app

# Run with environment variables
docker run -d -p 8080:80 \
    -e APP_ENV=development \
    --name test-container \
    php-user-app

# Run with volume mount (for development)
docker run -d -p 8080:80 \
    -v $(pwd):/var/www/html \
    --name test-container \
    php-user-app
```

### 3. Test the Application

```bash
# Check if container is running
docker ps

# View logs
docker logs test-container

# Test HTTP response
curl http://localhost:8080

# Execute commands inside container
docker exec -it test-container bash
docker exec test-container php -v
```

### 4. Inspect the Image

```bash
# View image details
docker inspect php-user-app

# View image history
docker history php-user-app

# Check image size
docker images php-user-app
```

### 5. Clean Up

```bash
# Stop container
docker stop test-container

# Remove container
docker rm test-container

# Remove image
docker rmi php-user-app
```

---

## Troubleshooting

### Build Fails: "Cannot find Dockerfile"

**Problem:** Docker can't find the Dockerfile.

**Solution:**
```bash
# Ensure you're in the directory with Dockerfile
cd /path/to/your/project

# Or specify Dockerfile path
docker build -f /path/to/Dockerfile -t php-user-app .
```

### Build Fails: "COPY failed"

**Problem:** Files don't exist or paths are wrong.

**Solution:**
```bash
# Check files exist
ls -la

# Use absolute paths or relative to build context
COPY ./index.php /var/www/html/
```

### Container Exits Immediately

**Problem:** Container starts then stops.

**Solution:**
```bash
# Check logs
docker logs container-name

# Common causes:
# - CMD command fails
# - Port conflict
# - Missing files
```

### Permission Denied Errors

**Problem:** Apache can't read/write files.

**Solution:**
```dockerfile
# Fix permissions in Dockerfile
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
```

### Port Already in Use

**Problem:** Port 8080 is already taken.

**Solution:**
```bash
# Use different port
docker run -d -p 3000:80 --name test-container php-user-app

# Or stop existing container
docker stop existing-container
```

### Image Too Large

**Problem:** Docker image is very large.

**Solution:**
- Use multi-stage builds
- Remove unnecessary files
- Use .dockerignore
- Clean apt cache: `rm -rf /var/lib/apt/lists/*`

---

## Advanced Examples

### Production-Ready Dockerfile

```dockerfile
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql zip

# Configure PHP
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/php.ini \
    && echo "upload_max_filesize = 10M" >> /usr/local/etc/php/php.ini \
    && echo "post_max_size = 10M" >> /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY --chown=www-data:www-data index.php .

# Enable Apache modules
RUN a2enmod rewrite headers

# Security: Remove Apache version info
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s \
    CMD curl -f http://localhost/ || exit 1

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
```

### Development Dockerfile with Xdebug

```dockerfile
FROM php:8.2-apache

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configure Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html

COPY index.php .

EXPOSE 80

CMD ["apache2-foreground"]
```

---

## Quick Reference

### Dockerfile Instructions

| Instruction | Purpose | Example |
|------------|---------|---------|
| `FROM` | Base image | `FROM php:8.2-apache` |
| `WORKDIR` | Set working directory | `WORKDIR /var/www/html` |
| `COPY` | Copy files | `COPY index.php .` |
| `ADD` | Copy files (with URL support) | `ADD https://example.com/file.txt .` |
| `RUN` | Execute command | `RUN apt-get update` |
| `CMD` | Default command | `CMD ["apache2-foreground"]` |
| `ENTRYPOINT` | Entry point command | `ENTRYPOINT ["php"]` |
| `ENV` | Environment variable | `ENV APP_ENV=production` |
| `ARG` | Build argument | `ARG VERSION=latest` |
| `EXPOSE` | Document port | `EXPOSE 80` |
| `VOLUME` | Create mount point | `VOLUME ["/data"]` |
| `USER` | Set user | `USER www-data` |
| `LABEL` | Add metadata | `LABEL version="1.0"` |

### Common Commands

```bash
# Build
docker build -t php-user-app .

# Run
docker run -d -p 8080:80 php-user-app

# Logs
docker logs container-name

# Execute
docker exec -it container-name bash

# Inspect
docker inspect image-name

# Remove
docker rmi image-name
```

---

**Next Steps:**
1. Create your Dockerfile
2. Test it locally
3. Integrate with Jenkins (see [JENKINS_DEPLOYMENT_GUIDE.md](JENKINS_DEPLOYMENT_GUIDE.md))
4. Deploy to production

---

**Need Help?** Check the main [README.md](README.md) or review Docker documentation at https://docs.docker.com/

