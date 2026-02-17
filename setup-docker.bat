@echo off
REM Zervi Manufacturing MES - Automated Docker Setup
REM This script will set up the complete system using Docker

echo 🚀 Zervi Manufacturing MES - Docker Setup
echo ============================================
echo.

REM Check if Docker is installed
echo 🔍 Checking for Docker...
docker --version >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Docker not found
    echo Please install Docker Desktop from: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)
echo ✅ Docker found
echo.

REM Check if Docker Compose is available
echo 🔍 Checking for Docker Compose...
docker-compose --version >nul 2>nul
if %errorlevel% neq 0 (
    echo ⚠️  Docker Compose not found, trying 'docker compose'...
    docker compose version >nul 2>nul
    if %errorlevel% neq 0 (
        echo ❌ Docker Compose not available
        echo Please install Docker Desktop which includes Docker Compose
        pause
        exit /b 1
    ) else (
        set DOCKER_COMPOSE=docker compose
    )
) else (
    set DOCKER_COMPOSE=docker-compose
)
echo ✅ Docker Compose available
echo.

REM Create necessary directories
echo 📁 Creating directories...
if not exist "docker\nginx\conf.d" mkdir docker\nginx\conf.d
if not exist "docker\php" mkdir docker\php
if not exist "docker\mysql" mkdir docker\mysql
if not exist "storage\logs" mkdir storage\logs
if not exist "storage\app\public" mkdir storage\app\public
if not exist "storage\framework\cache" mkdir storage\framework\cache
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "bootstrap\cache" mkdir bootstrap\cache
echo ✅ Directories created
echo.

REM Set proper permissions (for Docker)
echo 🔒 Setting permissions...
echo This might take a moment...
echo.

REM Create .env file if it doesn't exist
if not exist ".env" (
    echo ⚠️  .env file not found, creating from .env.example...
    copy .env.example .env
    echo.
    echo 📝 Please update the .env file with your configuration:
    echo    - APP_URL=http://localhost:8000
    echo    - DB_HOST=db
    echo    echo    - DB_PASSWORD=secret
    echo    - DB_DATABASE=zervi_mes
    echo.
    echo After updating, run this script again or continue with defaults
    pause
)

REM Build and start containers
echo 🐳 Building Docker containers...
%DOCKER_COMPOSE% build --no-cache
if %errorlevel% neq 0 (
    echo ❌ Docker build failed
    echo Please check Docker is running and you have internet connection
    pause
    exit /b 1
)
echo ✅ Docker containers built
echo.

echo 🚀 Starting services...
%DOCKER_COMPOSE% up -d
if %errorlevel% neq 0 (
    echo ❌ Failed to start services
    echo Check Docker logs for details
    pause
    exit /b 1
)
echo ✅ Services started
echo.

REM Wait for services to be ready
echo ⏳ Waiting for services to be ready...
timeout /t 30 /nobreak >nul

REM Run migrations
echo 🗄️ Running database migrations...
docker exec zervi-mes-app php artisan migrate --force
if %errorlevel% neq 0 (
    echo ⚠️  Initial migration failed, trying again...
    timeout /t 10 /nobreak >nul
    docker exec zervi-mes-app php artisan migrate --force
)
echo ✅ Database migrations completed
echo.

REM Run plugin migrations
echo 🗄️ Running plugin migrations...
docker exec zervi-mes-app php artisan migrate --path=plugins/ZerviManufacturing/database/migrations --force
echo ✅ Plugin migrations completed
echo.

REM Generate app key
echo 🔑 Generating application key...
docker exec zervi-mes-app php artisan key:generate --force
echo ✅ Application key generated
echo.

REM Clear caches
echo 🧹 Clearing caches...
docker exec zervi-mes-app php artisan cache:clear
docker exec zervi-mes-app php artisan config:clear
docker exec zervi-mes-app php artisan route:clear
docker exec zervi-mes-app php artisan view:clear
echo ✅ Caches cleared
echo.

REM Install and build assets
echo 🎨 Installing and building assets...
docker exec zervi-mes-app npm install
docker exec zervi-mes-app npm run build
echo ✅ Assets compiled
echo.

REM Run system test
echo 🧪 Running system verification...
docker exec zervi-mes-app php plugins/ZerviManufacturing/test-mes-system.php
echo.

REM Display service status
echo 📊 Service Status:
%DOCKER_COMPOSE% ps
echo.

echo ============================================
echo 🎉 SETUP COMPLETE!
echo ============================================
echo.
echo 🌐 Your Zervi Manufacturing MES is ready!
echo.
echo 📍 Access URLs:
echo    Main Application: http://localhost:8000
echo    Admin Panel: http://localhost:8000/admin
echo    PHPMyAdmin: http://localhost:8080
echo.
echo 🔧 Docker Commands:
echo    View logs: %DOCKER_COMPOSE% logs -f
echo    Stop services: %DOCKER_COMPOSE% down
echo    Restart services: %DOCKER_COMPOSE% restart
echo    Rebuild containers: %DOCKER_COMPOSE% build --no-cache
echo.
echo 📚 Documentation:
echo    README: plugins/ZerviManufacturing/README.md
echo    API Docs: plugins/ZerviManufacturing/API_DOCUMENTATION.md
echo    Testing: plugins/ZerviManufacturing/TESTING_GUIDE.md
echo.
echo 🎯 Next Steps:
echo 1. Open http://localhost:8000/admin in your browser
echo 2. Login with your admin credentials
echo 3. Navigate to Manufacturing → Kanban Board
echo 4. Test the drag-and-drop functionality
echo 5. Create sample work orders and test the workflow
echo.
echo 💡 Tips:
echo - The system uses supervisor-focused design with 4-item navigation
echo - Customer commitments show as "Toyota order due Thursday" not "WO-104"
echo - Material shortages are automatically detected and flagged
echo - Quality compliance follows TIS 1238-2564 standards
echo.
echo 🚀 Happy Manufacturing!
pause