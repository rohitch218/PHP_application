# Jenkins Deployment Guide

Complete step-by-step guide for deploying the PHP User Management Application using Jenkins with Docker.

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Jenkins Installation](#jenkins-installation)
3. [Docker Setup](#docker-setup)
4. [Understanding the Dockerfile](#understanding-the-dockerfile)
5. [Understanding the Jenkinsfile](#understanding-the-jenkinsfile)
6. [Creating Jenkins Pipeline](#creating-jenkins-pipeline)
7. [Running the Pipeline](#running-the-pipeline)
8. [Deploying the Application](#deploying-the-application)
9. [Troubleshooting](#troubleshooting)
10. [Best Practices](#best-practices)

---

## Prerequisites

Before starting, ensure you have:

- [ ] **Jenkins** installed (version 2.0 or higher)
- [ ] **Docker** installed and running
- [ ] **Git** installed
- [ ] **Java JDK** (for Jenkins)
- [ ] Access to your Git repository
- [ ] Basic knowledge of Jenkins and Docker

### Verify Prerequisites

```bash
# Check Jenkins
jenkins --version
# Or access Jenkins at http://localhost:8080

# Check Docker
docker --version
docker ps

# Check Git
git --version
```

---

## Jenkins Installation

### Option 1: Install Jenkins on Windows

1. **Download Jenkins:**
   - Visit: https://www.jenkins.io/download/
   - Download Windows installer (.msi)

2. **Install Jenkins:**
   - Run the installer
   - Follow the installation wizard
   - Jenkins will start automatically

3. **Initial Setup:**
   - Open browser: `http://localhost:8080`
   - Unlock Jenkins using the initial admin password
   - Install suggested plugins
   - Create admin user

### Option 2: Install Jenkins using Docker

```bash
# Run Jenkins in Docker
docker run -d \
  --name jenkins \
  -p 8080:8080 \
  -p 50000:50000 \
  -v jenkins_home:/var/jenkins_home \
  jenkins/jenkins:lts

# Get initial admin password
docker exec jenkins cat /var/jenkins_home/secrets/initialAdminPassword
```

### Option 3: Install Jenkins on Linux

```bash
# Ubuntu/Debian
wget -q -O - https://pkg.jenkins.io/debian-stable/jenkins.io.key | sudo apt-key add -
sudo sh -c 'echo deb http://pkg.jenkins.io/debian-stable binary/ > /etc/apt/sources.list.d/jenkins.list'
sudo apt-get update
sudo apt-get install jenkins

# Start Jenkins
sudo systemctl start jenkins
sudo systemctl enable jenkins
```

---

## Docker Setup

### Install Docker

#### Windows:
1. Download Docker Desktop from https://www.docker.com/products/docker-desktop
2. Install and restart your computer
3. Start Docker Desktop

#### Linux:
```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install docker.io
sudo systemctl start docker
sudo systemctl enable docker

# Add user to docker group (to run without sudo)
sudo usermod -aG docker $USER
# Log out and log back in
```

### Verify Docker Installation

```bash
docker --version
docker run hello-world
```

### Configure Jenkins to Use Docker

1. **Install Docker Plugin in Jenkins:**
   - Go to Jenkins Dashboard → Manage Jenkins → Manage Plugins
   - Search for "Docker Pipeline" plugin
   - Install and restart Jenkins

2. **Configure Docker in Jenkins:**
   - Go to Manage Jenkins → Configure System
   - Find "Docker" section
   - Add Docker installation (if needed)

3. **Grant Jenkins User Docker Permissions:**

   **On Linux:**
   ```bash
   sudo usermod -aG docker jenkins
   sudo systemctl restart jenkins
   ```

   **On Windows:**
   - Docker Desktop should work automatically
   - Ensure Docker Desktop is running

---

## Understanding the Dockerfile

### What is a Dockerfile?

A Dockerfile is a text file that contains instructions for building a Docker image. Our `Dockerfile` creates a containerized PHP application.

### Our Dockerfile Explained

```dockerfile
# Use official PHP Apache image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY index.php /var/www/html/

# Enable Apache mod_rewrite (if needed in future)
RUN a2enmod rewrite

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
```

### Line-by-Line Explanation

| Line | Purpose |
|------|---------|
| `FROM php:8.2-apache` | Base image with PHP 8.2 and Apache |
| `WORKDIR /var/www/html` | Sets the working directory |
| `COPY index.php` | Copies our PHP file into the container |
| `RUN a2enmod rewrite` | Enables Apache rewrite module |
| `RUN chown/chmod` | Sets file permissions |
| `EXPOSE 80` | Documents that port 80 is used |
| `CMD ["apache2-foreground"]` | Starts Apache server |

### Creating/Modifying the Dockerfile

1. **Open the Dockerfile in your editor**
2. **Modify as needed:**
   - Change PHP version: `FROM php:7.4-apache`
   - Add more files: `COPY *.php /var/www/html/`
   - Install extensions: `RUN docker-php-ext-install mysqli`
   - Add environment variables: `ENV APP_ENV=production`

3. **Test locally:**
   ```bash
   docker build -t php-user-app .
   docker run -d -p 8080:80 php-user-app
   ```

### Common Dockerfile Modifications

**Add PHP Extensions:**
```dockerfile
RUN docker-php-ext-install mysqli pdo pdo_mysql
```

**Add Environment Variables:**
```dockerfile
ENV APP_ENV=production
ENV DEBUG=false
```

**Copy Multiple Files:**
```dockerfile
COPY *.php /var/www/html/
COPY config/ /var/www/html/config/
```

---

## Understanding the Jenkinsfile

### What is a Jenkinsfile?

A Jenkinsfile is a text file that defines the Jenkins pipeline as code. It contains the entire CI/CD process.

### Our Jenkinsfile Explained

```groovy
pipeline {
    agent any
    
    environment {
        DOCKER_IMAGE = 'php-user-app'
        DOCKER_TAG = "${env.BUILD_NUMBER}"
    }
    
    stages {
        stage('Checkout') {
            steps {
                echo 'Checking out code from repository...'
                checkout scm
            }
        }
        
        stage('Build Docker Image') {
            steps {
                echo 'Building Docker image...'
                script {
                    sh "docker build -t ${DOCKER_IMAGE}:${DOCKER_TAG} ."
                    sh "docker tag ${DOCKER_IMAGE}:${DOCKER_TAG} ${DOCKER_IMAGE}:latest"
                }
            }
        }
        
        stage('Test Docker Image') {
            steps {
                echo 'Testing Docker image...'
                script {
                    sh """
                        docker run -d --name test-container -p 8080:80 ${DOCKER_IMAGE}:${DOCKER_TAG}
                        sleep 5
                        curl -f http://localhost:8080 || exit 1
                        docker stop test-container
                        docker rm test-container
                    """
                }
            }
        }
    }
    
    post {
        success {
            echo 'Pipeline completed successfully!'
            echo "Docker image: ${DOCKER_IMAGE}:${DOCKER_TAG}"
        }
        failure {
            echo 'Pipeline failed!'
        }
        always {
            echo 'Pipeline execution completed.'
        }
    }
}
```

### Section-by-Section Explanation

#### 1. Pipeline Declaration
```groovy
pipeline {
    agent any
```
- Defines a declarative pipeline
- `agent any` means run on any available Jenkins agent

#### 2. Environment Variables
```groovy
environment {
    DOCKER_IMAGE = 'php-user-app'
    DOCKER_TAG = "${env.BUILD_NUMBER}"
}
```
- Sets variables used throughout the pipeline
- `BUILD_NUMBER` is automatically provided by Jenkins

#### 3. Stages
Each `stage` represents a step in your CI/CD process:

**Stage 1: Checkout**
- Retrieves code from source control

**Stage 2: Build Docker Image**
- Builds Docker image with version tag
- Tags as both versioned and latest

**Stage 3: Test Docker Image**
- Runs container
- Tests HTTP response
- Cleans up test container

#### 4. Post Actions
- Runs after pipeline completes
- Different actions for success/failure/always

### Creating/Modifying the Jenkinsfile

1. **Open Jenkinsfile in your editor**

2. **Add a new stage:**
```groovy
stage('Deploy') {
    steps {
        script {
            sh "docker run -d -p 80:80 --name php-user-app ${DOCKER_IMAGE}:${DOCKER_TAG}"
        }
    }
}
```

3. **Add notifications:**
```groovy
post {
    success {
        emailext (
            subject: "Pipeline Success: ${env.JOB_NAME}",
            body: "Build ${env.BUILD_NUMBER} completed successfully!",
            to: "team@example.com"
        )
    }
}
```

4. **Add parallel stages:**
```groovy
stage('Test') {
    parallel {
        stage('Unit Tests') {
            steps {
                echo 'Running unit tests...'
            }
        }
        stage('Integration Tests') {
            steps {
                echo 'Running integration tests...'
            }
        }
    }
}
```

### Common Jenkinsfile Enhancements

**Add Docker Registry Push:**
```groovy
stage('Push to Registry') {
    steps {
        script {
            sh "docker login -u ${REGISTRY_USER} -p ${REGISTRY_PASS} ${REGISTRY_URL}"
            sh "docker push ${REGISTRY_URL}/${DOCKER_IMAGE}:${DOCKER_TAG}"
        }
    }
}
```

**Add Deployment Stage:**
```groovy
stage('Deploy to Production') {
    when {
        branch 'main'
    }
    steps {
        script {
            sh "docker stop php-user-app || true"
            sh "docker rm php-user-app || true"
            sh "docker run -d -p 80:80 --name php-user-app --restart unless-stopped ${DOCKER_IMAGE}:${DOCKER_TAG}"
        }
    }
}
```

**Add Slack Notifications:**
```groovy
post {
    success {
        slackSend(
            channel: '#deployments',
            color: 'good',
            message: "Pipeline ${env.JOB_NAME} #${env.BUILD_NUMBER} succeeded!"
        )
    }
}
```

---

## Creating Jenkins Pipeline

### Step 1: Prepare Your Repository

1. **Ensure files are in repository:**
   - `index.php`
   - `Dockerfile`
   - `Jenkinsfile`
   - `.dockerignore`

2. **Commit and push:**
   ```bash
   git add .
   git commit -m "Add Jenkins pipeline configuration"
   git push origin main
   ```

### Step 2: Create Pipeline Job in Jenkins

1. **Open Jenkins Dashboard:**
   - Navigate to `http://localhost:8080`

2. **Create New Item:**
   - Click "New Item" (or "Create a job")
   - Enter job name: `php-user-app-pipeline`
   - Select "Pipeline"
   - Click "OK"

3. **Configure Pipeline:**
   - Scroll to "Pipeline" section
   - **Definition**: Select "Pipeline script from SCM"
   - **SCM**: Select "Git"
   - **Repository URL**: Enter your Git repository URL
     - Example: `https://github.com/username/repo.git`
   - **Credentials**: Add if repository is private
   - **Branch**: `*/main` or `*/master`
   - **Script Path**: `Jenkinsfile` (default)

4. **Advanced Options (Optional):**
   - **Lightweight checkout**: Uncheck if you need full workspace
   - **Additional Behaviors**: Add if needed

5. **Save Configuration:**
   - Click "Save"

### Step 3: Configure Build Triggers (Optional)

1. **Poll SCM:**
   - Check "Poll SCM"
   - Schedule: `H/5 * * * *` (every 5 minutes)

2. **GitHub Webhook:**
   - In GitHub repository settings
   - Add webhook: `http://your-jenkins-url/github-webhook/`

3. **Build Periodically:**
   - Check "Build periodically"
   - Schedule: `0 2 * * *` (daily at 2 AM)

### Step 4: Configure Build Environment (Optional)

1. **Add Timestamps:**
   - Check "Add timestamps to the Console Output"

2. **Environment Variables:**
   - Add custom variables if needed

---

## Running the Pipeline

### Manual Execution

1. **Start Build:**
   - Go to your pipeline job
   - Click "Build Now"

2. **Monitor Progress:**
   - Click on the build number
   - View "Console Output" to see real-time logs
   - Watch each stage execute

3. **View Results:**
   - Green checkmark = Success
   - Red X = Failure
   - Click on stage to see details

### Understanding Build Output

**Successful Build:**
```
Started by user Admin
Running in Durability level: MAX_SURVIVABILITY
[Pipeline] Checkout
[Pipeline] Build Docker Image
[Pipeline] Test Docker Image
[Pipeline] post
Finished: SUCCESS
```

**Failed Build:**
```
[Pipeline] Build Docker Image
ERROR: docker build failed
[Pipeline] post
Finished: FAILURE
```

### Viewing Build Artifacts

1. Click on build number
2. Click "Artifacts" (if any)
3. Download files

---

## Deploying the Application

### Option 1: Deploy via Jenkins Pipeline

Add a deployment stage to your Jenkinsfile:

```groovy
stage('Deploy') {
    steps {
        script {
            // Stop existing container
            sh "docker stop php-user-app || true"
            sh "docker rm php-user-app || true"
            
            // Run new container
            sh """
                docker run -d \
                    --name php-user-app \
                    -p 80:80 \
                    --restart unless-stopped \
                    ${DOCKER_IMAGE}:${DOCKER_TAG}
            """
            
            // Health check
            sh "sleep 5"
            sh "curl -f http://localhost:80 || exit 1"
        }
    }
}
```

### Option 2: Manual Deployment After Build

1. **After successful build, SSH to server:**
   ```bash
   ssh user@your-server
   ```

2. **Pull and run container:**
   ```bash
   docker pull your-registry/php-user-app:latest
   docker stop php-user-app || true
   docker rm php-user-app || true
   docker run -d -p 80:80 --name php-user-app --restart unless-stopped your-registry/php-user-app:latest
   ```

### Option 3: Deploy to Remote Server via Jenkins

1. **Install SSH Plugin:**
   - Manage Jenkins → Manage Plugins
   - Install "SSH Pipeline Steps"

2. **Add deployment stage:**
```groovy
stage('Deploy to Server') {
    steps {
        script {
            sshagent(['your-ssh-credentials']) {
                sh """
                    ssh user@server 'docker stop php-user-app || true'
                    ssh user@server 'docker rm php-user-app || true'
                    ssh user@server 'docker run -d -p 80:80 --name php-user-app --restart unless-stopped php-user-app:latest'
                """
            }
        }
    }
}
```

---

## Troubleshooting

### Common Issues and Solutions

#### 1. Docker Command Not Found

**Error:**
```
docker: command not found
```

**Solution:**
- Ensure Docker is installed on Jenkins agent
- Add Docker to PATH in Jenkins configuration
- Restart Jenkins

#### 2. Permission Denied

**Error:**
```
permission denied while trying to connect to the Docker daemon socket
```

**Solution:**
```bash
# Add Jenkins user to docker group
sudo usermod -aG docker jenkins
sudo systemctl restart jenkins
```

#### 3. Port Already in Use

**Error:**
```
Bind for 0.0.0.0:8080 failed: port is already allocated
```

**Solution:**
```groovy
// Use different port or stop existing container
sh "docker stop test-container || true"
sh "docker rm test-container || true"
```

#### 4. Git Checkout Fails

**Error:**
```
Failed to connect to repository
```

**Solution:**
- Verify repository URL is correct
- Add credentials in Jenkins
- Check network connectivity
- Verify branch name

#### 5. Docker Build Fails

**Error:**
```
docker build failed
```

**Solution:**
- Check Dockerfile syntax
- Verify all files exist
- Check Docker daemon is running
- Review build logs for specific errors

#### 6. Test Stage Fails

**Error:**
```
curl: (7) Failed to connect to localhost port 8080
```

**Solution:**
- Increase sleep time before curl
- Check container logs: `docker logs test-container`
- Verify container is running: `docker ps`

### Debugging Tips

1. **Enable Verbose Logging:**
   ```groovy
   sh "docker build --progress=plain -t ${DOCKER_IMAGE} ."
   ```

2. **Check Container Logs:**
   ```groovy
   sh "docker logs test-container"
   ```

3. **Inspect Container:**
   ```groovy
   sh "docker inspect test-container"
   ```

4. **Test Manually:**
   ```bash
   # Run commands manually to isolate issues
   docker build -t php-user-app .
   docker run -d -p 8080:80 --name test php-user-app
   curl http://localhost:8080
   ```

---

## Best Practices

### 1. Version Control

- ✅ Always commit Jenkinsfile to repository
- ✅ Use version tags for Docker images
- ✅ Tag releases in Git

### 2. Security

- ✅ Use Jenkins credentials for sensitive data
- ✅ Don't hardcode passwords
- ✅ Use Docker secrets for production

### 3. Performance

- ✅ Use Docker layer caching
- ✅ Clean up old images
- ✅ Use multi-stage builds for smaller images

### 4. Reliability

- ✅ Add health checks
- ✅ Implement rollback procedures
- ✅ Monitor pipeline execution
- ✅ Set up notifications

### 5. Documentation

- ✅ Document custom stages
- ✅ Add comments in Jenkinsfile
- ✅ Maintain deployment runbooks

### Example: Enhanced Jenkinsfile with Best Practices

```groovy
pipeline {
    agent any
    
    options {
        timeout(time: 30, unit: 'MINUTES')
        timestamps()
        ansiColor('xterm')
    }
    
    environment {
        DOCKER_IMAGE = 'php-user-app'
        DOCKER_TAG = "${env.BUILD_NUMBER}"
        DOCKER_REGISTRY = credentials('docker-registry-url')
    }
    
    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }
        
        stage('Build') {
            steps {
                script {
                    sh """
                        docker build \
                            --tag ${DOCKER_IMAGE}:${DOCKER_TAG} \
                            --tag ${DOCKER_IMAGE}:latest \
                            .
                    """
                }
            }
        }
        
        stage('Test') {
            steps {
                script {
                    sh """
                        docker run -d --name test-container -p 8080:80 ${DOCKER_IMAGE}:${DOCKER_TAG}
                        sleep 10
                        curl -f http://localhost:8080 || exit 1
                        docker stop test-container
                        docker rm test-container
                    """
                }
            }
        }
        
        stage('Push') {
            when {
                branch 'main'
            }
            steps {
                script {
                    withCredentials([usernamePassword(credentialsId: 'docker-credentials', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                        sh """
                            echo ${DOCKER_PASS} | docker login -u ${DOCKER_USER} --password-stdin ${DOCKER_REGISTRY}
                            docker push ${DOCKER_REGISTRY}/${DOCKER_IMAGE}:${DOCKER_TAG}
                            docker push ${DOCKER_REGISTRY}/${DOCKER_IMAGE}:latest
                        """
                    }
                }
            }
        }
        
        stage('Deploy') {
            when {
                branch 'main'
            }
            steps {
                script {
                    sh """
                        docker stop php-user-app || true
                        docker rm php-user-app || true
                        docker run -d \
                            --name php-user-app \
                            -p 80:80 \
                            --restart unless-stopped \
                            ${DOCKER_IMAGE}:${DOCKER_TAG}
                    """
                }
            }
        }
    }
    
    post {
        always {
            cleanWs()
            sh "docker system prune -f"
        }
        success {
            echo "Pipeline succeeded!"
        }
        failure {
            echo "Pipeline failed!"
        }
    }
}
```

---

## Quick Reference Commands

### Jenkins CLI

```bash
# List jobs
jenkins-cli list-jobs

# Build job
jenkins-cli build php-user-app-pipeline

# Get build status
jenkins-cli get-build php-user-app-pipeline 1
```

### Docker Commands

```bash
# Build image
docker build -t php-user-app .

# Run container
docker run -d -p 8080:80 --name php-user-app php-user-app

# View logs
docker logs php-user-app

# Stop container
docker stop php-user-app

# Remove container
docker rm php-user-app

# List images
docker images

# Remove image
docker rmi php-user-app
```

---

## Next Steps

1. ✅ Set up Jenkins and Docker
2. ✅ Create pipeline job
3. ✅ Run first build
4. ✅ Add deployment stage
5. ✅ Configure notifications
6. ✅ Set up automated triggers

---

**Need Help?** Check the main [README.md](README.md) or review Jenkins console logs for detailed error messages.

