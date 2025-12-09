# GitHub CI/CD Setup Guide

This document explains how to set up your Laravel project to run on GitHub.

## Overview

The project is configured with GitHub Actions to automatically run tests, code style checks, and build assets on every push and pull request.

## What's Included

### 1. GitHub Actions Workflow (`.github/workflows/ci.yml`)

The workflow performs the following checks:

- **PHP Setup**: Uses PHP 8.2 with required extensions
- **Node.js Setup**: Uses Node.js 20 for frontend assets
- **Dependency Installation**: Installs Composer and npm packages
- **Environment Setup**: Copies `.env.example` to `.env`
- **Database Migration**: Runs Laravel migrations
- **Tests**: Runs Pest test suite
- **Code Style**: Checks code formatting with Laravel Pint
- **Type Checking**: Analyzes code with PHPStan
- **Build**: Builds frontend assets with Vite

## Prerequisites

Before pushing to GitHub, ensure:

1. **Local Testing**
   ```bash
   php artisan migrate
   php artisan test
   npm run build
   ```

2. **Git Configuration**
   - Initialize git: `git init`
   - Add remote: `git remote add origin <your-repo-url>`
   - Create initial commit: `git add . && git commit -m "Initial commit"`

## Local Development Setup

To set up your project locally:

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file
cp .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Run database migrations
php artisan migrate

# 5. Install Node dependencies
npm install

# 6. Build frontend assets
npm run build
```

## GitHub Repository Configuration

### Required Files

✅ **Already configured:**
- `.env.example` - Contains environment variables template
- `.gitignore` - Excludes unnecessary files
- `composer.json` & `package.json` - Dependency management
- `.github/workflows/ci.yml` - CI/CD pipeline

### Important

⚠️ **Never commit `.env` file to Git!**

The workflow automatically:
- Copies `.env.example` → `.env`
- Generates application key
- Runs migrations with `--force` flag

## Customization

### Add Environment Variables to GitHub

If you need to use environment variables in CI:

1. Go to GitHub repository **Settings** → **Secrets and variables** → **Actions**
2. Click **New repository secret**
3. Add your variables (e.g., `DATABASE_URL`, `API_KEYS`)

Then reference in your GitHub Actions workflow:
```yaml
env:
  DATABASE_URL: ${{ secrets.DATABASE_URL }}
```

### Modify the Workflow

Edit `.github/workflows/ci.yml` to:
- Change PHP version in `setup-php.step`
- Add additional test commands
- Add deployment steps
- Configure notifications

## Troubleshooting

### Common Issues

**Issue: Migrations fail in CI**
- Solution: Use `--force` flag (already configured)
- Check `.env.example` has correct database settings

**Issue: Tests fail locally but pass in CI**
- Solution: Run `composer install` and `npm install` locally
- Check `.env` configuration matches CI environment

**Issue: Build fails with "npm: command not found"**
- Solution: Node.js version might be outdated
- Update `node-version` in workflow to match your needs

**Issue: PHP extensions missing**
- Solution: Add extensions in `shivammathur/setup-php` step
- List available extensions in the action documentation

## Testing Locally Before Pushing

```bash
# Run all tests
php artisan pest

# Run specific test file
php artisan pest tests/Feature/YourTest.php

# Check code style
php artisan pint --test

# Build assets
npm run build

# Verify migrations
php artisan migrate:fresh
php artisan migrate:reset
```

## Continuous Integration Status

Check your workflow status:
- GitHub Repository → **Actions** tab
- Click on any workflow run to see details
- Red badge = failed, Green badge = passed

## Next Steps

1. Push your code: `git push origin main`
2. GitHub Actions will automatically run
3. Check the **Actions** tab for results
4. Fix any failing checks and push again

---

For more information:
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Testing](https://laravel.com/docs/testing)
- [Pest Documentation](https://pestphp.com)
