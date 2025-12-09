# Contributing to this Project

## Getting Started

1. **Fork the repository** on GitHub
2. **Clone your fork** locally:
   ```bash
   git clone https://github.com/YOUR_USERNAME/my-library.git
   cd my-library
   ```

3. **Create a feature branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```

4. **Set up your development environment**:
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   ```

## Code Standards

### PHP Code Style

We use [Laravel Pint](https://laravel.com/docs/pint) for code formatting:

```bash
# Check style
php artisan pint --test

# Fix style issues automatically
php artisan pint
```

### JavaScript/CSS

Follow standard conventions for JavaScript and CSS. Tailwind CSS is configured for styling.

## Testing

All code must have tests:

```bash
# Run all tests
php artisan pest

# Run with coverage
php artisan pest --coverage

# Run specific test
php artisan pest tests/Feature/ExampleTest.php

# Run in parallel
php artisan pest --parallel
```

## Creating a Pull Request

1. **Push your changes**:
   ```bash
   git push origin feature/your-feature-name
   ```

2. **Create a Pull Request** on GitHub

3. **PR Guidelines**:
   - Clear, descriptive title
   - Description of changes made
   - Reference related issues
   - Ensure all checks pass

4. **Wait for review** - maintainers will review and merge

## Commit Messages

Use clear, descriptive commit messages:

```
Good:   "Add user authentication feature"
Bad:    "Fix stuff" or "Update"

Good:   "Fix migration error for categories table"
Bad:    "migrations"
```

## Pre-commit Checklist

Before pushing, ensure:

- [ ] Code passes `php artisan pint --test`
- [ ] All tests pass: `php artisan pest`
- [ ] No sensitive data in code
- [ ] `.env` file is not committed
- [ ] Code is properly documented

## Questions?

If you have questions:
1. Check existing issues and documentation
2. Ask in pull request comments
3. Create an issue with detailed description

Thank you for contributing! 🙏
