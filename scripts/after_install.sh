#!/bin/bash
# After Install Script for AWS CodeDeploy

echo "After Install: Setting up application..."

# Navigate to deployment directory
cd /opt/codedeploy-agent/deployment-root/$DEPLOYMENT_GROUP_ID/$DEPLOYMENT_ID/deployment-archive

# Build Docker image
echo "Building Docker image..."
docker build -t php-user-app:latest .

echo "After Install: Completed successfully"

