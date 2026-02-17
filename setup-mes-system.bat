@echo off
REM Zervi Manufacturing MES - Automated Setup Script
REM This script will set up the complete development environment

echo 🚀 Zervi Manufacturing MES - Automated Setup
echo ============================================
echo.

REM Check if we're in the right directory
if not exist "artisan" (
    echo ❌ Error: Please run this script from the aureuserp root directory
    echo    Current directory: %cd%
    echo    Expected to find: artisan file
    pause
    exit /b 1
)

echo 📍 Current directory: %cd%
echo.

REM Step 1: Check for Node.js
echo 🔍 Checking for Node.js...
where node >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ Node.js found
    node --version
) else (
    echo ❌ Node.js not found
    echo Please install Node.js from: https://nodejs.org/
    pause
    exit /b 1
)
echo.

REM Step 2: Check for Git
echo 🔍 Checking for Git...
where git >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ Git found
    git --version
) else (
    echo ❌ Git not found
    echo Please install Git from: https://git-scm.com/
    pause
    exit /b 1
)
echo.

REM Step 3: Try to find PHP
echo 🔍 Searching for PHP...
set PHP_FOUND=0

REM Check common PHP locations
set PHP_PATHS=
set "PHP_PATHS=%PHP_PATHS%;C:\php\php.exe"
set "PHP_PATHS=%PHP_PATHS%;C:\xampp\php\php.exe"
set "PHP_PATHS=%PHP_PATHS%;C:\wamp\bin\php\php8.2\php.exe"
set "PHP_PATHS=%PHP_PATHS%;C:\Program Files\PHP\php.exe"
set "PHP_PATHS=%PHP_PATHS%;C:\Program Files (x86)\PHP\php.exe"

for %%p in (%PHP_PATHS%) do (
    if exist "%%p" (
        echo ✅ PHP found at: %%p
        set "PHP_EXECUTABLE=%%p"
        set PHP_FOUND=1
        goto :php_found
    )
)

REM Check if PHP is in PATH
where php >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ PHP found in PATH
    set "PHP_EXECUTABLE=php"
    set PHP_FOUND=1
) else (
    echo ❌ PHP not found in common locations
    echo Please install PHP 8.2+ and add it to your PATH
    echo Or install XAMPP/WAMP which includes PHP
    pause
    exit /b 1
)

:php_found
echo.

REM Step 4: Check PHP version
echo 🔍 Checking PHP version...
"%PHP_EXECUTABLE%" -v
if %errorlevel% neq 0 (
    echo ❌ PHP execution failed
    pause
    exit /b 1
)
echo.

REM Step 5: Check for Composer
echo 🔍 Checking for Composer...
where composer >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ Composer found
    composer --version
) else (
    echo ⚠️  Composer not found - will attempt to install
    echo Downloading Composer...
    
    REM Download Composer installer
    powershell -Command "Invoke-WebRequest -Uri 'https://getcomposer.org/installer' -OutFile 'composer-setup.php'"
    
    REM Install Composer globally
    "%PHP_EXECUTABLE%" composer-setup.php --install-dir="C:\ProgramData\ComposerSetup" --filename=composer
    
    REM Add to PATH
    setx PATH "%PATH%;C:\ProgramData\ComposerSetup" /M
    
    REM Clean up
    del composer-setup.php
    
    echo ✅ Composer installed
)
echo.

REM Step 6: Install PHP dependencies
echo 📦 Installing PHP dependencies...
call composer install --no-dev --optimize-autoloader
if %errorlevel% neq 0 (
    echo ❌ Composer install failed
    pause
    exit /b 1
)
echo ✅ PHP dependencies installed
echo.

REM Step 7: Install Node.js dependencies
echo 📦 Installing Node.js dependencies...
call npm install
if %errorlevel% neq 0 (
    echo ❌ npm install failed
    pause
    exit /b 1
)
echo ✅ Node.js dependencies installed
echo.

REM Step 8: Check environment file
echo 🔍 Checking environment configuration...
if not exist ".env" (
    echo ⚠️  .env file not found, copying from .env.example...
    copy .env.example .env
    echo.
    echo ⚠️  Please edit .env file with your database configuration:
    echo    - DB_CONNECTION
    echo    - DB_HOST
    echo    echo    - DB_PORT
    echo    - DB_DATABASE
    echo    - DB_USERNAME
    echo    - DB_PASSWORD
    echo.
    echo After editing, run this script again or manually run:
    echo   php artisan key:generate
    echo   php artisan migrate
    pause
    exit /b 0
)
echo ✅ .env file exists
echo.

REM Step 9: Generate application key
echo 🔑 Generating application key...
"%PHP_EXECUTABLE%" artisan key:generate
if %errorlevel% neq 0 (
    echo ❌ Key generation failed
    pause
    exit /b 1
)
echo ✅ Application key generated
echo.

REM Step 10: Run migrations
echo 🗄️ Running database migrations...
"%PHP_EXECUTABLE%" artisan migrate
if %errorlevel% neq 0 (
    echo ❌ Migration failed - check database configuration
    echo Make sure your database is running and credentials are correct
    pause
    exit /b 1
)
echo ✅ Database migrations completed
echo.

REM Step 11: Run plugin-specific migrations
echo 🗄️ Running plugin migrations...
"%PHP_EXECUTABLE%" artisan migrate --path=plugins/ZerviManufacturing/database/migrations
if %errorlevel% neq 0 (
    echo ❌ Plugin migrations failed
    pause
    exit /b 1
)
echo ✅ Plugin migrations completed
echo.

REM Step 12: Clear caches
echo 🧹 Clearing caches...
"%PHP_EXECUTABLE%" artisan cache:clear
"%PHP_EXECUTABLE%" artisan config:clear
"%PHP_EXECUTABLE%" artisan route:clear
"%PHP_EXECUTABLE%" artisan view:clear
echo ✅ Caches cleared
echo.

REM Step 13: Compile assets
echo 🎨 Compiling frontend assets...
call npm run build
if %errorlevel% neq 0 (
    echo ⚠️  Asset compilation failed - trying development build...
    call npm run dev
)
echo ✅ Frontend assets compiled
echo.

REM Step 14: Run system test
echo 🧪 Running system verification test...
"%PHP_EXECUTABLE%" plugins/ZerviManufacturing/test-mes-system.php
echo.

REM Step 15: Start development server
echo 🚀 Starting Laravel development server...
echo.
echo ============================================
echo 🎉 SETUP COMPLETE!
echo ============================================
echo.
echo Your Zervi Manufacturing MES is ready!
echo.
echo 📋 Next steps:
echo 1. The development server will start on http://localhost:8000
echo 2. Access the admin panel at: http://localhost:8000/admin
echo 3. Test the Kanban board functionality
echo 4. Create sample work orders and test the workflow
echo.
echo 🛑 Press Ctrl+C to stop the server when you're done testing
echo.
echo Starting server now...
echo.

REM Start the development server
"%PHP_EXECUTABLE%" artisan serve --host=0.0.0.0 --port=8000

echo.
echo Setup completed successfully! 🎉
pause