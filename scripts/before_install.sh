#!/bin/bash
# Before Install Script for AWS CodeDeploy

echo "Before Install: Starting deployment preparation..."

# Update system packages
yum update -y

# Install Docker if not already installed
if ! command -v docker &> /dev/null; then
    echo "Installing Docker..."
    yum install -y docker
    systemctl start docker
    systemctl enable docker
fi

# Install Docker Compose if needed
if ! command -v docker-compose &> /dev/null; then
    echo "Installing Docker Compose..."
    curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

# Stop any existing containers
docker stop php-user-app 2>/dev/null || true
docker rm php-user-app 2>/dev/null || true

echo "Before Install: Completed successfully"

