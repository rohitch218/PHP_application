# AWS CodePipeline Setup Guide

This guide will help you set up AWS CodePipeline for automated deployment of the PHP User Management Application.

## Prerequisites

1. AWS Account with appropriate permissions
2. AWS CLI configured with credentials
3. Source code in AWS CodeCommit, GitHub, or S3
4. Docker installed on your local machine (for testing)

## Architecture Overview

```
CodeCommit/GitHub → CodePipeline → CodeBuild → ECR → ECS/EC2 → Application
```

## Step 1: Create Amazon ECR Repository

1. Navigate to Amazon ECR in AWS Console
2. Click "Create repository"
3. Repository name: `php-user-app`
4. Visibility: Private
5. Click "Create repository"
6. Note the repository URI (format: `ACCOUNT_ID.dkr.ecr.REGION.amazonaws.com/php-user-app`)

## Step 2: Create IAM Roles

### CodeBuild Service Role

1. Go to IAM Console → Roles → Create Role
2. Select "AWS service" → "CodeBuild"
3. Attach policies:
   - `AmazonEC2ContainerRegistryPowerUser`
   - `CloudWatchLogsFullAccess`
   - `AmazonS3FullAccess` (if using S3 artifacts)
4. Name: `CodeBuild-PHP-User-App-Role`
5. Note the Role ARN

### CodePipeline Service Role

1. Go to IAM Console → Roles → Create Role
2. Select "AWS service" → "CodePipeline"
3. Attach policies:
   - `AWSCodePipelineFullAccess`
   - `AmazonEC2ContainerRegistryFullAccess`
   - `AmazonS3FullAccess`
4. Name: `CodePipeline-PHP-User-App-Role`
5. Note the Role ARN

### CodeDeploy Service Role (if using EC2)

1. Go to IAM Console → Roles → Create Role
2. Select "AWS service" → "CodeDeploy"
3. Attach policies:
   - `AWSCodeDeployRole`
4. Name: `CodeDeploy-PHP-User-App-Role`
5. Note the Role ARN

## Step 3: Create CodeBuild Project

1. Navigate to CodeBuild Console
2. Click "Create build project"
3. Configure:
   - **Project name**: `php-user-app-build`
   - **Source**: Select your source (CodeCommit/GitHub/S3)
   - **Environment**:
     - Managed image
     - Operating system: Ubuntu
     - Runtime: Standard
     - Image: aws/codebuild/standard:7.0
     - Privileged: ✅ Enable (required for Docker)
   - **Service role**: Select the CodeBuild role created in Step 2
   - **Buildspec**: Use `buildspec.yml` from repository
   - **Environment variables**:
     ```
     AWS_DEFAULT_REGION = us-east-1 (or your region)
     AWS_ACCOUNT_ID = YOUR_ACCOUNT_ID
     IMAGE_REPO_NAME = php-user-app
     IMAGE_TAG = latest
     ```
4. Click "Create build project"

## Step 4: Create CodePipeline

### Option A: Using AWS Console

1. Navigate to CodePipeline Console
2. Click "Create pipeline"
3. **Pipeline settings**:
   - Pipeline name: `php-user-app-pipeline`
   - Service role: Select the CodePipeline role created in Step 2
4. **Source stage**:
   - Source provider: AWS CodeCommit / GitHub / S3
   - Repository: Select your repository
   - Branch: main/master
   - Output artifact format: CodePipeline default
5. **Build stage**:
   - Build provider: AWS CodeBuild
   - Project name: `php-user-app-build`
   - Build type: Single build
6. **Deploy stage** (choose one):

   **Option 1: Amazon ECS**
   - Deploy provider: Amazon ECS
   - Cluster name: Your ECS cluster
   - Service name: Your ECS service
   - Image filename: `imagedefinitions.json`

   **Option 2: Amazon EC2 with CodeDeploy**
   - Deploy provider: AWS CodeDeploy
   - Application name: Create new or select existing
   - Deployment group: Create new or select existing
   - Input artifact: BuildArtifact

7. Click "Create pipeline"

### Option B: Using AWS CLI

```bash
# Create pipeline using pipeline.json
aws codepipeline create-pipeline --cli-input-json file://pipeline.json
```

## Step 5: Configure Deployment Target

### Option A: Deploy to Amazon ECS

1. Create ECS Cluster:
   ```bash
   aws ecs create-cluster --cluster-name php-user-app-cluster
   ```

2. Create Task Definition (see `ecs-task-definition.json`)

3. Create ECS Service:
   ```bash
   aws ecs create-service \
     --cluster php-user-app-cluster \
     --service-name php-user-app-service \
     --task-definition php-user-app \
     --desired-count 1 \
     --launch-type FARGATE \
     --network-configuration "awsvpcConfiguration={subnets=[subnet-xxx],securityGroups=[sg-xxx],assignPublicIp=ENABLED}"
   ```

### Option B: Deploy to Amazon EC2

1. Launch EC2 instance with:
   - Amazon Linux 2 AMI
   - IAM role with CodeDeploy permissions
   - Security group allowing HTTP (port 80)

2. Install CodeDeploy agent:
   ```bash
   sudo yum update -y
   sudo yum install ruby wget -y
   cd /home/ec2-user
   wget https://aws-codedeploy-us-east-1.s3.us-east-1.amazonaws.com/latest/install
   chmod +x ./install
   sudo ./install auto
   sudo service codedeploy-agent start
   ```

3. Create CodeDeploy Application:
   ```bash
   aws deploy create-application \
     --application-name php-user-app \
     --compute-platform Server
   ```

4. Create Deployment Group:
   ```bash
   aws deploy create-deployment-group \
     --application-name php-user-app \
     --deployment-group-name php-user-app-dg \
     --service-role-arn arn:aws:iam::ACCOUNT_ID:role/CodeDeploy-PHP-User-App-Role \
     --ec2-tag-filters Key=Name,Value=php-user-app,Type=KEY_AND_VALUE
   ```

## Step 6: Environment Variables for CodeBuild

Update CodeBuild project with these environment variables:

```bash
AWS_DEFAULT_REGION=us-east-1
AWS_ACCOUNT_ID=123456789012
IMAGE_REPO_NAME=php-user-app
IMAGE_TAG=latest
```

## Step 7: Test the Pipeline

1. Make a commit to your repository
2. CodePipeline should automatically trigger
3. Monitor the pipeline execution in AWS Console
4. Check build logs in CodeBuild
5. Verify deployment in ECS/EC2

## Troubleshooting

### Build Fails
- Check CodeBuild logs
- Verify ECR repository exists
- Ensure IAM permissions are correct
- Check Docker build logs

### Deployment Fails
- Verify target environment (ECS/EC2) is accessible
- Check CodeDeploy agent status (for EC2)
- Review deployment logs
- Verify security group rules

### Image Push Fails
- Verify ECR login credentials
- Check IAM permissions for ECR
- Ensure repository URI is correct

## Cleanup

To remove all resources:

```bash
# Delete pipeline
aws codepipeline delete-pipeline --name php-user-app-pipeline

# Delete build project
aws codebuild delete-project --name php-user-app-build

# Delete ECR repository
aws ecr delete-repository --repository-name php-user-app --force

# Delete ECS service and cluster (if used)
aws ecs delete-service --cluster php-user-app-cluster --service php-user-app-service --force
aws ecs delete-cluster --cluster php-user-app-cluster
```

