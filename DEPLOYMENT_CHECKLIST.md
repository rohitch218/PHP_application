# Deployment Checklist

Use this checklist to ensure all components are properly configured before deployment.

## ✅ Pre-Deployment Checklist

### General
- [ ] Source code is committed to repository
- [ ] All configuration files are present
- [ ] Docker image builds successfully locally
- [ ] Application runs correctly in Docker container
- [ ] Port 80 (or chosen port) is available

### For Jenkins Deployment
- [ ] Jenkins is installed and running
- [ ] Docker is installed on Jenkins agent
- [ ] Jenkins user has Docker permissions
- [ ] Required Jenkins plugins installed:
  - [ ] Docker Pipeline
  - [ ] Git
  - [ ] Docker
- [ ] Jenkinsfile is in repository root
- [ ] Git repository is accessible from Jenkins
- [ ] Jenkins pipeline job is created and configured

### For AWS CodePipeline Deployment
- [ ] AWS account is active
- [ ] AWS CLI is configured with credentials
- [ ] IAM roles are created:
  - [ ] CodeBuild service role
  - [ ] CodePipeline service role
  - [ ] CodeDeploy service role (if using EC2)
- [ ] ECR repository is created
- [ ] CodeBuild project is created
- [ ] Environment variables are set in CodeBuild:
  - [ ] AWS_DEFAULT_REGION
  - [ ] AWS_ACCOUNT_ID
  - [ ] IMAGE_REPO_NAME
  - [ ] IMAGE_TAG
- [ ] CodePipeline is created and configured
- [ ] Source repository is connected
- [ ] Build stage is configured
- [ ] Deploy stage is configured

### For ECS Deployment
- [ ] ECS cluster is created
- [ ] Task definition is registered
- [ ] ECS service is created
- [ ] Security groups are configured
- [ ] VPC and subnets are configured (for Fargate)
- [ ] CloudWatch log group is created

### For EC2 Deployment
- [ ] EC2 instance is launched
- [ ] Security group allows HTTP (port 80)
- [ ] CodeDeploy agent is installed
- [ ] CodeDeploy agent is running
- [ ] CodeDeploy application is created
- [ ] Deployment group is created
- [ ] IAM instance profile is attached

## 🧪 Testing Checklist

### Local Testing
- [ ] Docker image builds without errors
- [ ] Container starts successfully
- [ ] Application is accessible via browser
- [ ] All user data displays correctly
- [ ] Styling renders properly
- [ ] No console errors in browser

### Pipeline Testing
- [ ] Source stage completes successfully
- [ ] Build stage completes successfully
- [ ] Docker image is created
- [ ] Image is pushed to registry (if configured)
- [ ] Test stage passes (if configured)
- [ ] Deploy stage completes successfully

### Post-Deployment Testing
- [ ] Application is accessible at deployment URL
- [ ] All pages load correctly
- [ ] No errors in application logs
- [ ] Health checks pass
- [ ] Performance is acceptable

## 🔍 Verification Steps

### Verify Docker Image
```bash
docker images | grep php-user-app
docker run -d -p 8080:80 --name test php-user-app
curl http://localhost:8080
docker stop test && docker rm test
```

### Verify Jenkins Pipeline
1. Check Jenkins console output
2. Verify all stages complete
3. Check Docker image exists: `docker images`
4. Verify container runs: `docker ps`

### Verify AWS Deployment
```bash
# Check ECR image
aws ecr describe-images --repository-name php-user-app

# Check ECS service
aws ecs describe-services --cluster php-user-app-cluster --services php-user-app-service

# Check CodePipeline status
aws codepipeline get-pipeline-state --name php-user-app-pipeline
```

## 🚨 Common Issues to Check

- [ ] Docker daemon is running
- [ ] Port conflicts resolved
- [ ] IAM permissions are correct
- [ ] Security groups allow traffic
- [ ] Network connectivity is established
- [ ] Environment variables are set
- [ ] Logs are accessible
- [ ] Resource limits are adequate

## 📊 Monitoring Checklist

- [ ] CloudWatch logs are configured (AWS)
- [ ] Application logs are accessible
- [ ] Error tracking is set up
- [ ] Health check endpoint is configured
- [ ] Alerts are configured (if needed)

## 🔄 Rollback Plan

- [ ] Previous version is tagged
- [ ] Rollback procedure is documented
- [ ] Rollback can be executed quickly
- [ ] Data backup is available (if applicable)

## 📝 Post-Deployment

- [ ] Update deployment documentation
- [ ] Notify stakeholders
- [ ] Monitor application for issues
- [ ] Review deployment logs
- [ ] Document any issues encountered
- [ ] Update version numbers

---

**Last Updated**: Check before each deployment
**Next Review**: After each deployment

