#!/bin/bash
# Application Start Script for AWS CodeDeploy

echo "Application Start: Starting PHP application container..."

# Navigate to deployment directory
cd /opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive

# Run Docker container
docker run -d \
    --name php-user-app \
    -p 80:80 \
    --restart unless-stopped \
    php-user-app:latest

# Wait for container to be ready
sleep 5

# Health check
if docker ps | grep -q php-user-app; then
    echo "Application Start: Container started successfully"
    docker ps | grep php-user-app
else
    echo "Application Start: Failed to start container"
    exit 1
fi

