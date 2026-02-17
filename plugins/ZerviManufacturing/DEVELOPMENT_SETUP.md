# Zervi Manufacturing MES - Development Setup Guide

## 🏠 Home Development Environment Setup

This guide will help you set up the Zervi Manufacturing MES system for development work from home.

## 📋 Prerequisites

### Required Software
1. **PHP 8.1+** (with required extensions)
2. **Composer** (PHP package manager)
3. **Node.js 16+** (for frontend assets)
4. **MySQL 8.0+** or **PostgreSQL 13+**
5. **Git** (for version control)

### Recommended IDE
- **VS Code** with PHP extensions
- **PHPStorm** (if you have license)
- **Laravel IDE Helper** for autocomplete

## 🚀 Quick Start

### Step 1: Clone the Repository
```bash
git clone https://github.com/sakimotto/aureuserp.git
cd aureuserp
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Install plugin dependencies
cd plugins/ZerviManufacturing
composer install
cd ../..
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=aureuserp
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

### Step 4: Database Setup
```bash
# Run migrations
php artisan migrate

# Run plugin migrations specifically
php artisan migrate --path=plugins/ZerviManufacturing/database/migrations

# Seed initial data (if available)
php artisan db:seed
```

### Step 5: Asset Compilation
```bash
# Compile frontend assets
npm run dev

# For production build
npm run build
```

### Step 6: Start Development Server
```bash
# Start Laravel development server
php artisan serve --port=8000

# The application will be available at:
# http://localhost:8000
```

## 🔧 Development Configuration

### PHP Configuration
Ensure your `php.ini` has these settings:
```ini
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
```

### Database Configuration
For MySQL, ensure these settings:
```sql
-- Increase max packet size
SET GLOBAL max_allowed_packet=268435456;

-- For better performance
SET GLOBAL innodb_buffer_pool_size=268435456;
```

### Laravel Configuration
In your `.env` file, add these development settings:
```env
APP_DEBUG=true
APP_URL=http://localhost:8000

# Cache configuration for development
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mail configuration (use mailtrap for testing)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

## 🧪 Testing Setup

### PHPUnit Configuration
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=ZerviManufacturing

# Run with coverage (requires Xdebug)
php artisan test --coverage
```

### Browser Testing (Dusk)
```bash
# Install Dusk
composer require --dev laravel/dusk

# Run Dusk installer
php artisan dusk:install

# Run browser tests
php artisan dusk
```

### Code Quality Tools
```bash
# PHP CS Fixer (code formatting)
composer require --dev friendsofphp/php-cs-fixer

# PHPStan (static analysis)
composer require --dev phpstan/phpstan

# Run code quality checks
./vendor/bin/php-cs-fixer fix --dry-run
./vendor/bin/phpstan analyse
```

## 📁 Project Structure for Development

```
aureuserp/
├── app/                    # Core Laravel application
├── config/                 # Configuration files
├── database/               # Migrations and seeders
├── plugins/                # Plugin directory
│   └── ZerviManufacturing/
│       ├── src/            # Plugin source code
│       ├── database/       # Plugin migrations
│       ├── resources/      # Plugin views and assets
│       └── tests/          # Plugin tests
├── public/                 # Public assets
├── resources/              # Frontend assets
├── routes/                 # Application routes
├── storage/                # Logs, cache, uploads
└── vendor/                 # Composer dependencies
```

## 🔄 Git Workflow

### Branch Strategy
```bash
# Create feature branch
git checkout -b feature/new-kanban-feature

# Make changes and commit
git add .
git commit -m "Add new Kanban feature"

# Push to remote
git push origin feature/new-kanban-feature

# Create pull request on GitHub
```

### Commit Message Convention
```
type(scope): description

Examples:
feat(kanban): add drag-and-drop functionality
fix(materials): resolve shortage calculation bug
docs(readme): update installation instructions
```

## 🐛 Common Development Issues

### Port 8000 Already in Use
```bash
# Find process using port 8000
netstat -ano | findstr :8000

# Kill the process (replace PID with actual process ID)
taskkill /PID 1234 /F

# Or use different port
php artisan serve --port=8080
```

### Database Connection Issues
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Clear database cache
php artisan cache:clear
php artisan config:clear
```

### Composer Memory Issues
```bash
# Increase memory limit
COMPOSER_MEMORY_LIMIT=2G composer install

# Or use this command
php -d memory_limit=2G /path/to/composer install
```

### Plugin Not Loading
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-register plugins
php artisan plugin:discover
```

## 🔍 Debugging Tools

### Laravel Debugbar
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Telescope (Laravel Debug Tool)
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Tinker (Interactive Shell)
```bash
# Start interactive shell
php artisan tinker

# Test database queries
>>> $orders = App\Models\WorkOrder::all();
>>> $orders->count();
```

## 📊 Performance Monitoring

### Query Optimization
```bash
# Enable query logging
php artisan db:monitor

# Check slow queries
php artisan db:show-slow-queries
```

### Cache Monitoring
```bash
# Clear specific caches
php artisan cache:forget key
php artisan cache:flush

# View cache statistics
php artisan cache:stats
```

## 🚀 Deployment Preparation

### Environment Check
```bash
# Check environment configuration
php artisan env

# Verify all services
php artisan health:check
```

### Asset Optimization
```bash
# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Compile and optimize assets
npm run production

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📚 Learning Resources

### Laravel Learning
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel News](https://laravel-news.com)
- [Laracasts](https://laracasts.com)

### Filament Learning
- [Filament Documentation](https://filamentphp.com/docs)
- [Filament Examples](https://filamentphp.com/docs/3.x/panels/resources)

### PHP Best Practices
- [PHP The Right Way](https://phptherightway.com)
- [PSR Standards](https://www.php-fig.org/psr)

## 🤝 Getting Help

### Internal Documentation
- Check the plugin README.md for specific features
- Review the implementation-plan.md for architecture decisions
- Look at existing code for examples and patterns

### External Support
- Laravel Community Forum
- Stack Overflow (tag: laravel, filamentphp)
- GitHub Issues for bug reports

## 📋 Daily Development Checklist

### Before Starting Work
- [ ] Pull latest changes: `git pull origin main`
- [ ] Install dependencies: `composer install && npm install`
- [ ] Clear caches: `php artisan cache:clear`
- [ ] Start development server: `php artisan serve`

### During Development
- [ ] Write tests for new features
- [ ] Follow coding standards (PSR-12)
- [ ] Test in multiple browsers
- [ ] Check for JavaScript console errors
- [ ] Verify mobile responsiveness

### Before Committing
- [ ] Run tests: `php artisan test`
- [ ] Check code quality: `./vendor/bin/phpstan analyse`
- [ ] Format code: `./vendor/bin/php-cs-fixer fix`
- [ ] Test manually in browser
- [ ] Update documentation if needed

### After Work
- [ ] Commit changes with descriptive messages
- [ ] Push to remote repository
- [ ] Create pull request for review
- [ ] Document any issues encountered

## 🎯 Next Steps

1. **Explore the Codebase**: Start with the Kanban board implementation
2. **Test Features**: Try the drag-and-drop functionality
3. **Customize**: Modify departments or add new features
4. **Integrate**: Connect with existing inventory systems
5. **Deploy**: Set up production environment

---

**Happy Coding! 🚀**

For questions or issues, check the main README.md or create an issue on GitHub.