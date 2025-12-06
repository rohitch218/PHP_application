# Project Structure Overview

## 📂 Complete File Structure

```
PHP/
│
├── 📄 index.php                          # Main PHP application with user table
├── 🐳 Dockerfile                          # Docker container configuration
├── 📝 .dockerignore                      # Files excluded from Docker builds
│
├── 🔄 CI/CD Configuration Files
│   ├── Jenkinsfile                       # Jenkins pipeline definition
│   ├── buildspec.yml                     # AWS CodeBuild specification
│   ├── appspec.yml                       # AWS CodeDeploy specification
│   ├── pipeline.json                     # AWS CodePipeline template
│   └── ecs-task-definition.json          # ECS task definition template
│
├── 📜 Deployment Scripts
│   └── scripts/
│       ├── before_install.sh             # Pre-deployment setup
│       ├── after_install.sh              # Post-installation tasks
│       ├── application_start.sh          # Application startup
│       └── application_stop.sh           # Application shutdown
│
└── 📚 Documentation
    ├── README.md                         # Complete project documentation
    ├── QUICK_START.md                    # Quick start guide
    ├── aws-pipeline-setup.md             # AWS CodePipeline setup guide
    ├── DEPLOYMENT_CHECKLIST.md           # Deployment checklist
    └── PROJECT_STRUCTURE.md              # This file
```

## 📋 File Descriptions

### Application Files

| File | Purpose | Used By |
|------|---------|---------|
| `index.php` | Main PHP application displaying user data | All deployments |
| `Dockerfile` | Docker image configuration | Docker, Jenkins, AWS |

### CI/CD Configuration

| File | Purpose | Used By |
|------|---------|---------|
| `Jenkinsfile` | Jenkins pipeline stages and steps | Jenkins |
| `buildspec.yml` | AWS CodeBuild build instructions | AWS CodeBuild |
| `appspec.yml` | AWS CodeDeploy deployment specification | AWS CodeDeploy |
| `pipeline.json` | AWS CodePipeline structure template | AWS CodePipeline |
| `ecs-task-definition.json` | ECS container task definition | AWS ECS |

### Deployment Scripts

| Script | Purpose | When Executed |
|--------|---------|---------------|
| `before_install.sh` | Install dependencies, setup environment | Before installation |
| `after_install.sh` | Build Docker image | After installation |
| `application_start.sh` | Start application container | Application start |
| `application_stop.sh` | Stop application container | Application stop |

### Documentation

| Document | Purpose | Audience |
|----------|---------|----------|
| `README.md` | Complete project documentation | All users |
| `QUICK_START.md` | Fast setup instructions | New users |
| `aws-pipeline-setup.md` | Detailed AWS setup guide | AWS users |
| `DEPLOYMENT_CHECKLIST.md` | Pre-deployment verification | DevOps teams |
| `PROJECT_STRUCTURE.md` | Project organization overview | Developers |

## 🔄 Deployment Flow

### Jenkins Flow
```
Repository → Jenkins → Docker Build → Test → (Optional) Deploy
```

### AWS CodePipeline Flow
```
Repository → CodePipeline → CodeBuild → ECR → ECS/EC2 → Application
```

## 🎯 Quick Reference

### To run locally:
```bash
docker build -t php-user-app . && docker run -d -p 8080:80 php-user-app
```

### To set up Jenkins:
1. Create Pipeline job
2. Point to repository
3. Set script path to `Jenkinsfile`

### To set up AWS:
1. Follow `aws-pipeline-setup.md`
2. Create ECR repository
3. Create CodeBuild project
4. Create CodePipeline

## 📊 File Size Estimates

- `index.php`: ~5 KB
- `Dockerfile`: ~0.5 KB
- `Jenkinsfile`: ~1.5 KB
- `buildspec.yml`: ~1 KB
- Documentation: ~50 KB total

## 🔐 Security Notes

- No sensitive data in repository
- IAM roles required for AWS
- Docker images should be scanned
- Use environment variables for secrets

## 🚀 Next Steps

1. Review `README.md` for complete documentation
2. Use `QUICK_START.md` for immediate setup
3. Follow `DEPLOYMENT_CHECKLIST.md` before deploying
4. Refer to `aws-pipeline-setup.md` for AWS configuration

---

**Last Updated**: Project creation
**Maintained By**: Development Team

