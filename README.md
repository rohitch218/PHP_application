# PHP User Management Application

A simple, modern PHP application that displays dummy user data in a beautiful, responsive table format. The application is fully containerized with Docker and supports automated CI/CD pipelines using both Jenkins and AWS CodePipeline.

## 📋 Table of Contents

- [Features](#features)
- [Architecture](#architecture)
- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Docker Deployment](#docker-deployment)
- [Jenkins CI/CD](#jenkins-cicd)
- [AWS CodePipeline Deployment](#aws-codepipeline-deployment)
- [Project Structure](#project-structure)
- [Configuration](#configuration)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

- **Modern UI**: Beautiful, responsive design with gradient styling
- **User Data Display**: Shows 10 dummy users with complete information
- **Dockerized**: Fully containerized application for easy deployment
- **CI/CD Ready**: Supports both Jenkins and AWS CodePipeline
- **Status Indicators**: Color-coded status badges (Active, Inactive, Pending)
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices

## 🏗️ Architecture

```
┌─────────────────┐
│   Source Code   │
│  (Git/GitHub)   │
└────────┬────────┘
         │
         ├─────────────────┬─────────────────┐
         │                 │                 │
         ▼                 ▼                 ▼
    ┌─────────┐      ┌──────────┐      ┌──────────────┐
    │ Jenkins │      │CodePipeline│    │  Local Docker│
    └────┬────┘      └─────┬─────┘      └──────┬───────┘
         │                 │                    │
         ▼                 ▼                    ▼
    ┌─────────┐      ┌──────────┐      ┌──────────────┐
    │  Docker │      │ CodeBuild│      │   Container  │
    │  Image  │      │   + ECR  │      │   Running    │
    └────┬────┘      └─────┬─────┘      └──────┬───────┘
         │                 │                    │
         └─────────────────┴────────────────────┘
                           │
                           ▼
                    ┌──────────────┐
                    │  Deployment  │
                    │  (ECS/EC2)   │
                    └──────────────┘
```

## 📦 Prerequisites

### For Local Development
- PHP 7.4+ (or use Docker)
- Web server (Apache/Nginx) or Docker
- Git

### For Docker Deployment
- Docker Engine 20.10+
- Docker Compose (optional)

### For Jenkins Pipeline
- Jenkins 2.0+
- Docker installed on Jenkins agent
- Jenkins plugins: Docker Pipeline, Git

### For AWS CodePipeline
- AWS Account with appropriate permissions
- AWS CLI configured
- Source code in AWS CodeCommit, GitHub, or S3
- ECR repository (for container registry)
- ECS cluster or EC2 instance (for deployment)

## 🚀 Quick Start

### Local Development (Without Docker)

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd PHP
   ```

2. **Start PHP built-in server:**
   ```bash
   php -S localhost:8000
   ```

3. **Access the application:**
   Open your browser and navigate to `http://localhost:8000`

### Docker Quick Start

1. **Build the Docker image:**
   ```bash
   docker build -t php-user-app .
   ```

2. **Run the container:**
   ```bash
   docker run -d -p 8080:80 --name php-user-app php-user-app
   ```

3. **Access the application:**
   Open your browser and navigate to `http://localhost:8080`

4. **Stop the container:**
   ```bash
   docker stop php-user-app
   docker rm php-user-app
   ```

## 🐳 Docker Deployment

### Building the Image

```bash
# Build with default tag
docker build -t php-user-app .

# Build with specific tag
docker build -t php-user-app:v1.0.0 .

# Build with custom registry
docker build -t your-registry/php-user-app:latest .
```

### Running the Container

```bash
# Basic run
docker run -d -p 8080:80 --name php-user-app php-user-app

# Run with custom port
docker run -d -p 3000:80 --name php-user-app php-user-app

# Run with restart policy
docker run -d -p 8080:80 --restart unless-stopped --name php-user-app php-user-app

# Run with environment variables (if needed in future)
docker run -d -p 8080:80 -e ENV_VAR=value --name php-user-app php-user-app
```

### Docker Compose (Optional)

Create a `docker-compose.yml` file:

```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8080:80"
    container_name: php-user-app
    restart: unless-stopped
```

Run with:
```bash
docker-compose up -d
```

### Pushing to Docker Registry

```bash
# Tag for registry
docker tag php-user-app:latest your-registry/php-user-app:latest

# Login to registry
docker login your-registry

# Push image
docker push your-registry/php-user-app:latest
```

## 🔄 Jenkins CI/CD

### Setup Instructions

1. **Install Required Jenkins Plugins:**
   - Docker Pipeline
   - Git
   - Docker

2. **Configure Jenkins:**
   - Go to Jenkins Dashboard → Manage Jenkins → Configure System
   - Ensure Docker is installed on Jenkins agent
   - Configure Docker credentials if using private registry

3. **Create Pipeline Job:**
   - Click "New Item" → Select "Pipeline"
   - Name: `php-user-app-pipeline`
   - Configure:
     - **Pipeline Definition**: Pipeline script from SCM
     - **SCM**: Git
     - **Repository URL**: Your Git repository URL
     - **Branch**: `*/main` or `*/master`
     - **Script Path**: `Jenkinsfile`

4. **Run the Pipeline:**
   - Click "Build Now"
   - Monitor the build progress
   - Check console output for logs

### Pipeline Stages

The Jenkins pipeline includes:

1. **Checkout**: Retrieves code from source control
2. **Build Docker Image**: Builds the Docker image with version tag
3. **Test Docker Image**: Runs container and tests HTTP response
4. **Post Actions**: Success/failure notifications

### Customizing the Pipeline

Edit `Jenkinsfile` to:
- Add deployment stages
- Integrate with Docker registry
- Add notification steps (Slack, Email)
- Add security scanning
- Deploy to different environments

Example addition for deployment:
```groovy
stage('Deploy') {
    steps {
        script {
            sh "docker push ${DOCKER_IMAGE}:${DOCKER_TAG}"
            // Add deployment commands here
        }
    }
}
```

## ☁️ AWS CodePipeline Deployment

### Overview

AWS CodePipeline automates the build, test, and deployment process. The pipeline consists of:
- **Source**: CodeCommit, GitHub, or S3
- **Build**: CodeBuild (builds Docker image and pushes to ECR)
- **Deploy**: ECS or EC2 with CodeDeploy

### Detailed Setup Guide

See [AWS Pipeline Setup Guide](aws-pipeline-setup.md) for complete step-by-step instructions.

### Quick Setup Steps

1. **Create ECR Repository:**
   ```bash
   aws ecr create-repository --repository-name php-user-app
   ```

2. **Create CodeBuild Project:**
   - Use the provided `buildspec.yml`
   - Configure environment variables (see `aws-pipeline-setup.md`)

3. **Create CodePipeline:**
   - Use AWS Console or `pipeline.json` template
   - Configure source, build, and deploy stages

4. **Deploy to ECS:**
   - Create ECS cluster and service
   - Use `ecs-task-definition.json` as template

5. **Deploy to EC2:**
   - Launch EC2 instance
   - Install CodeDeploy agent
   - Create CodeDeploy application and deployment group

### Environment Variables for CodeBuild

Configure these in CodeBuild project:

```
AWS_DEFAULT_REGION=us-east-1
AWS_ACCOUNT_ID=123456789012
IMAGE_REPO_NAME=php-user-app
IMAGE_TAG=latest
```

### Deployment Options

#### Option 1: Amazon ECS (Fargate)

1. Create ECS cluster
2. Register task definition
3. Create ECS service
4. Configure CodePipeline to deploy to ECS

#### Option 2: Amazon EC2 with CodeDeploy

1. Launch EC2 instance
2. Install CodeDeploy agent
3. Create CodeDeploy application
4. Configure CodePipeline to deploy to EC2

## 📁 Project Structure

```
.
├── index.php                 # Main PHP application
├── Dockerfile               # Docker configuration
├── .dockerignore           # Docker ignore file
├── Jenkinsfile             # Jenkins pipeline definition
├── buildspec.yml          # AWS CodeBuild specification
├── appspec.yml            # AWS CodeDeploy specification
├── pipeline.json          # AWS CodePipeline template
├── ecs-task-definition.json # ECS task definition
├── scripts/               # Deployment scripts
│   ├── before_install.sh
│   ├── after_install.sh
│   ├── application_start.sh
│   └── application_stop.sh
├── aws-pipeline-setup.md  # AWS setup documentation
└── README.md             # This file
```

## ⚙️ Configuration

### Docker Configuration

The `Dockerfile` uses:
- Base image: `php:8.2-apache`
- Working directory: `/var/www/html`
- Exposed port: `80`
- Apache mod_rewrite enabled

### Application Configuration

The application displays 10 dummy users with:
- ID
- Name
- Email
- Phone
- Department
- Status (Active/Inactive/Pending)

To modify user data, edit the `$users` array in `index.php`.

### CI/CD Configuration

- **Jenkins**: Configured via `Jenkinsfile`
- **AWS CodePipeline**: Configured via `buildspec.yml` and `pipeline.json`
- **CodeDeploy**: Configured via `appspec.yml` and scripts in `scripts/`

## 🔧 Troubleshooting

### Docker Issues

**Problem**: Container won't start
- **Solution**: Check Docker logs: `docker logs php-user-app`
- Verify port 80 is not in use: `netstat -an | grep 80`

**Problem**: Permission denied errors
- **Solution**: Ensure Docker daemon is running: `docker ps`
- Check file permissions in Dockerfile

### Jenkins Issues

**Problem**: Pipeline fails at Docker build
- **Solution**: 
  - Verify Docker is installed on Jenkins agent
  - Check Jenkins has permission to use Docker
  - Review Jenkins console output for errors

**Problem**: Cannot connect to Docker daemon
- **Solution**: Add Jenkins user to docker group: `sudo usermod -aG docker jenkins`

### AWS CodePipeline Issues

**Problem**: CodeBuild fails
- **Solution**:
  - Check build logs in CodeBuild console
  - Verify IAM permissions
  - Ensure ECR repository exists
  - Check environment variables are set correctly

**Problem**: Deployment fails
- **Solution**:
  - Verify target environment (ECS/EC2) is accessible
  - Check CodeDeploy agent status (for EC2)
  - Review deployment logs
  - Verify security group rules allow traffic

**Problem**: ECR push fails
- **Solution**:
  - Verify ECR login: `aws ecr get-login-password --region REGION | docker login --username AWS --password-stdin ACCOUNT_ID.dkr.ecr.REGION.amazonaws.com`
  - Check IAM permissions for ECR
  - Ensure repository URI is correct

### Application Issues

**Problem**: Page not loading
- **Solution**: 
  - Check container is running: `docker ps`
  - Verify port mapping: `docker port php-user-app`
  - Check Apache logs: `docker logs php-user-app`

**Problem**: Styling not working
- **Solution**: 
  - Clear browser cache
  - Check browser console for errors
  - Verify all CSS is inline (no external dependencies)

## 🧪 Testing

### Manual Testing

1. **Build test:**
   ```bash
   docker build -t php-user-app:test .
   ```

2. **Run test:**
   ```bash
   docker run -d -p 8080:80 --name test-container php-user-app:test
   ```

3. **Health check:**
   ```bash
   curl http://localhost:8080
   ```

4. **Cleanup:**
   ```bash
   docker stop test-container
   docker rm test-container
   ```

### Automated Testing

Add to Jenkinsfile or buildspec.yml:
```bash
# Test HTTP response
curl -f http://localhost:8080 || exit 1

# Test specific content
curl http://localhost:8080 | grep -q "User Management System" || exit 1
```

## 📝 Best Practices

1. **Security:**
   - Use environment variables for sensitive data
   - Implement proper authentication (if needed)
   - Keep Docker images updated
   - Scan images for vulnerabilities

2. **Performance:**
   - Use multi-stage Docker builds (if needed)
   - Implement caching strategies
   - Optimize image size

3. **Monitoring:**
   - Set up CloudWatch logs (AWS)
   - Monitor container health
   - Track deployment metrics

4. **Version Control:**
   - Tag Docker images with version numbers
   - Use semantic versioning
   - Maintain changelog

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is provided as-is for educational and demonstration purposes.

## 📚 Additional Documentation

Comprehensive guides for different aspects of the project:

- **[JENKINS_DEPLOYMENT_GUIDE.md](JENKINS_DEPLOYMENT_GUIDE.md)** - Complete step-by-step guide for Jenkins setup, Dockerfile creation, and Jenkinsfile configuration
- **[DOCKERFILE_GUIDE.md](DOCKERFILE_GUIDE.md)** - Detailed guide for creating, understanding, and modifying Dockerfiles
- **[aws-pipeline-setup.md](aws-pipeline-setup.md)** - AWS CodePipeline setup and configuration guide
- **[QUICK_START.md](QUICK_START.md)** - Quick reference for immediate setup
- **[DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)** - Pre-deployment verification checklist
- **[PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md)** - Project organization overview

## 📞 Support

For issues and questions:
- Check the [Troubleshooting](#troubleshooting) section
- Review [JENKINS_DEPLOYMENT_GUIDE.md](JENKINS_DEPLOYMENT_GUIDE.md) for Jenkins-specific help
- Review [DOCKERFILE_GUIDE.md](DOCKERFILE_GUIDE.md) for Docker-related issues
- Review pipeline logs
- Check AWS CloudWatch logs (for AWS deployments)

## 🎯 Future Enhancements

- [ ] Add database integration
- [ ] Implement user authentication
- [ ] Add REST API endpoints
- [ ] Implement user CRUD operations
- [ ] Add unit and integration tests
- [ ] Set up monitoring and alerting
- [ ] Add multi-environment support (dev, staging, prod)
- [ ] Implement blue-green deployments

---

**Built with ❤️ using PHP, Docker, Jenkins, and AWS**
