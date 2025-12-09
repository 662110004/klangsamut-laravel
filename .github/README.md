# Project Setup for GitHub

This folder contains GitHub-related configuration files for continuous integration and best practices.

## Files Included

### `.github/workflows/ci.yml`
**Automated CI/CD Pipeline**

Runs on every push and pull request to:
- Install PHP and Node.js dependencies
- Run database migrations
- Execute test suite (Pest)
- Check code style (Laravel Pint)
- Analyze code types (PHPStan)
- Build frontend assets

### `.github/dependabot.yml`
**Automated Dependency Updates**

Automatically creates pull requests for:
- Composer package updates (weekly)
- npm package updates (weekly)
- GitHub Actions updates (monthly)

## Quick Start

### For New Contributors

1. **Windows Users:**
   ```cmd
   setup.bat
   ```

2. **macOS/Linux Users:**
   ```bash
   bash setup.sh
   ```

3. **Manual Setup:**
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   npm run build
   ```

### For Development

```bash
# Start development server
php artisan serve

# Watch for changes in frontend
npm run dev

# Run tests
php artisan pest

# Check code style
php artisan pint --test

# Fix code style
php artisan pint
```

## GitHub Workflow Badges

Add to your README to show CI status:

```markdown
[![CI](https://github.com/YOUR_USERNAME/my-library/actions/workflows/ci.yml/badge.svg)](https://github.com/YOUR_USERNAME/my-library/actions)
```

## Troubleshooting CI Failures

### ❌ Tests Failing

1. Run tests locally: `php artisan pest`
2. Check test output in GitHub Actions → Actions tab
3. Review the specific failing test
4. Fix the code and push again

### ❌ Code Style Issues

1. Run formatter: `php artisan pint`
2. Commit changes: `git add . && git commit -m "Fix code style"`
3. Push: `git push`

### ❌ Database Migration Errors

1. Check `.env.example` database settings
2. Run locally: `php artisan migrate:fresh`
3. Look for syntax errors in migration files
4. Fix and push

## Documentation Files

- **`GITHUB_SETUP.md`** - Detailed GitHub setup guide
- **`CONTRIBUTING.md`** - Guidelines for contributors
- **`README.md`** - Main project documentation

## Environment Variables

To use secrets in workflows:

1. Go to Repository Settings → Secrets and variables → Actions
2. Click "New repository secret"
3. Add secret (e.g., `DATABASE_URL`, `API_KEY`)
4. Use in workflow: `${{ secrets.YOUR_SECRET_NAME }}`

## Support

For questions or issues:
1. Check `GITHUB_SETUP.md`
2. Check `CONTRIBUTING.md`
3. Review GitHub Actions documentation
4. Create an issue in the repository

---

**Remember:** Never commit `.env` file to Git! Always use `.env.example` as template.
