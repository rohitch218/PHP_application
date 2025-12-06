pipeline {
    agent any
    
    environment {
        DOCKER_IMAGE = 'php-user-app'
        DOCKER_TAG = "${env.BUILD_NUMBER}"
    }
    
    stages {
         stage('Checkout') {
            steps {
                git branch: 'main',
                    url: 'https://github.com/rohitch218/PHP_application.git'
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

