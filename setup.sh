#!/bin/bash

# Laravel Setup Script for Local Development
# This script automates the initial setup process

set -e

echo "🚀 Starting Laravel project setup..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer is not installed${NC}"
    echo "Install from: https://getcomposer.org/download/"
    exit 1
fi

# Check if node is installed
if ! command -v node &> /dev/null; then
    echo -e "${RED}❌ Node.js is not installed${NC}"
    echo "Install from: https://nodejs.org/"
    exit 1
fi

echo -e "${GREEN}✓ Composer and Node.js found${NC}"

# Install PHP dependencies
echo -e "\n${YELLOW}📦 Installing PHP dependencies...${NC}"
composer install

# Install Node dependencies
echo -e "\n${YELLOW}📦 Installing Node.js dependencies...${NC}"
npm install

# Setup environment file
if [ ! -f .env ]; then
    echo -e "\n${YELLOW}🔧 Setting up environment file...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✓ .env file created${NC}"
else
    echo -e "${GREEN}✓ .env file already exists${NC}"
fi

# Generate application key
echo -e "\n${YELLOW}🔑 Generating application key...${NC}"
php artisan key:generate

# Run database migrations
echo -e "\n${YELLOW}🗄️  Running database migrations...${NC}"
php artisan migrate

# Build frontend assets
echo -e "\n${YELLOW}🏗️  Building frontend assets...${NC}"
npm run build

echo -e "\n${GREEN}✅ Setup complete!${NC}"
echo -e "\n${YELLOW}Next steps:${NC}"
echo "1. Review .env file and update configuration if needed"
echo "2. Run: php artisan serve"
echo "3. Visit: http://localhost:8000"
echo "4. Run tests: php artisan pest"
echo ""
echo -e "${GREEN}Happy coding! 🎉${NC}"
