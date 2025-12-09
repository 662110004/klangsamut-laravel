@echo off
REM Laravel Setup Script for Windows
REM This script automates the initial setup process

setlocal enabledelayedexpansion

echo.
echo 🚀 Starting Laravel project setup...
echo.

REM Check if composer is installed
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Composer is not installed
    echo Install from: https://getcomposer.org/download/
    pause
    exit /b 1
)

REM Check if node is installed
where node >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Node.js is not installed
    echo Install from: https://nodejs.org/
    pause
    exit /b 1
)

echo ✓ Composer and Node.js found
echo.

REM Install PHP dependencies
echo 📦 Installing PHP dependencies...
call composer install
if %errorlevel% neq 0 goto error

REM Install Node dependencies
echo.
echo 📦 Installing Node.js dependencies...
call npm install
if %errorlevel% neq 0 goto error

REM Setup environment file
if not exist .env (
    echo.
    echo 🔧 Setting up environment file...
    copy .env.example .env
    echo ✓ .env file created
) else (
    echo.
    echo ✓ .env file already exists
)

REM Generate application key
echo.
echo 🔑 Generating application key...
call php artisan key:generate
if %errorlevel% neq 0 goto error

REM Run database migrations
echo.
echo 🗄️  Running database migrations...
call php artisan migrate
if %errorlevel% neq 0 goto error

REM Build frontend assets
echo.
echo 🏗️  Building frontend assets...
call npm run build
if %errorlevel% neq 0 goto error

echo.
echo ✅ Setup complete!
echo.
echo 📋 Next steps:
echo 1. Review .env file and update configuration if needed
echo 2. Run: php artisan serve
echo 3. Visit: http://localhost:8000
echo 4. Run tests: php artisan pest
echo.
echo 🎉 Happy coding!
echo.
pause
exit /b 0

:error
echo.
echo ❌ Setup failed! Please check the error above.
pause
exit /b 1
