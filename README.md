# PHP User Management Application

A simple PHP application that displays dummy user data in a beautiful, responsive table format.

## Features

- Displays 10 dummy users with their information
- Modern, responsive UI design
- Dockerized application
- Jenkins CI/CD pipeline support

## Prerequisites

- Docker installed on your system
- Jenkins (for CI/CD pipeline)

## Running with Docker

### Build the Docker image:
```bash
docker build -t php-user-app .
```

### Run the container:
```bash
docker run -d -p 8080:80 --name php-user-app php-user-app
```

### Access the application:
Open your browser and navigate to: `http://localhost:8080`

### Stop and remove the container:
```bash
docker stop php-user-app
docker rm php-user-app
```

## Jenkins Pipeline

The project includes a `Jenkinsfile` that automates:
1. Code checkout
2. Docker image building
3. Image testing
4. Cleanup of old images

### Setting up Jenkins Pipeline:

1. Create a new Pipeline job in Jenkins
2. Configure the job to use "Pipeline script from SCM"
3. Point to your Git repository containing this project
4. Set the script path to `Jenkinsfile`
5. Run the pipeline

## Project Structure

```
.
├── index.php          # Main PHP application file
├── Dockerfile         # Docker configuration
├── Jenkinsfile        # Jenkins pipeline definition
├── .dockerignore      # Files to exclude from Docker build
└── README.md          # This file
```

## Technologies Used

- PHP 8.2
- Apache Web Server
- Docker
- Jenkins

## License

This is a demo application for educational purposes.

