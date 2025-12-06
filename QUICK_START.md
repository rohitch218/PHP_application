# Quick Start Guide

## 🚀 Fastest Way to Get Started

### Option 1: Docker (Recommended for Quick Testing)

```bash
# Build and run in one command
docker build -t php-user-app . && docker run -d -p 8080:80 --name php-user-app php-user-app

# Access at http://localhost:8080
```

### Option 2: Local PHP Server

```bash
# Run PHP built-in server
php -S localhost:8000

# Access at http://localhost:8000
```

## 📋 Deployment Options Summary

| Method | Best For | Setup Time | Complexity |
|--------|----------|------------|------------|
| **Docker** | Local development, testing | 2 min | ⭐ Easy |
| **Jenkins** | On-premise CI/CD | 15 min | ⭐⭐ Medium |
| **AWS CodePipeline** | Cloud-native deployments | 30 min | ⭐⭐⭐ Advanced |

## 🔧 Prerequisites Checklist

### For Docker:
- [ ] Docker installed
- [ ] Port 8080 available

### For Jenkins:
- [ ] Jenkins installed
- [ ] Docker on Jenkins agent
- [ ] Git repository access

### For AWS:
- [ ] AWS account
- [ ] AWS CLI configured
- [ ] IAM permissions set
- [ ] ECR repository created

## 📚 Documentation Links

- **Full Documentation**: [README.md](README.md)
- **AWS Setup Guide**: [aws-pipeline-setup.md](aws-pipeline-setup.md)
- **Jenkins Setup**: See README.md → Jenkins CI/CD section

## 🆘 Need Help?

1. Check [Troubleshooting](README.md#troubleshooting) section
2. Review pipeline logs
3. Verify prerequisites are met

