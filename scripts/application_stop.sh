#!/bin/bash
# Application Stop Script for AWS CodeDeploy

echo "Application Stop: Stopping PHP application container..."

# Stop and remove existing container
docker stop php-user-app 2>/dev/null || true
docker rm php-user-app 2>/dev/null || true

echo "Application Stop: Completed successfully"

