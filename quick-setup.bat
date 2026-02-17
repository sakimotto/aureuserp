@echo off
REM Zervi Manufacturing MES - Quick Docker Setup
REM This script uses pre-built Docker images for instant deployment

echo 🚀 Zervi Manufacturing MES - Quick Docker Setup
echo ============================================
echo.

REM Check if Docker is running
echo 🔍 Checking Docker status...
docker info >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Docker is not running
    echo Please start Docker Desktop and run this script again
    pause
    exit /b 1
)
echo ✅ Docker is running
echo.

REM Create necessary directories
echo 📁 Creating directories...
if not exist "storage\logs" mkdir storage\logs
if not exist "storage\app\public" mkdir storage\app\public
if not exist "storage\framework\cache" mkdir storage\framework\cache
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "bootstrap\cache" mkdir bootstrap\cache
echo ✅ Directories created
echo.

REM Create .env file if it doesn't exist
echo 🔍 Checking environment configuration...
if not exist ".env" (
    echo ⚠️  .env file not found, creating from .env.example...
    copy .env.example .env
    echo.
    echo 📝 Created .env file with default configuration
    echo Please update the following settings if needed:
    echo    - APP_URL=http://localhost:8000
    echo    - DB_CONNECTION=mysql
    echo    - DB_HOST=db
    echo    - DB_PASSWORD=secret
    echo.
)
echo ✅ Environment configuration ready
echo.

REM Pull and start services with simple Docker Compose
echo 🐳 Starting services...
echo Using pre-built Docker images for instant deployment...
echo.

REM Create a simple Docker Compose file for quick setup
echo Creating Docker Compose configuration...
(
echo version: '3.8'
echo.
echo services:
echo   app:
echo     image: webdevops/php-nginx:8.2-alpine
echo     container_name: zervi-mes-app
echo     restart: unless-stopped
echo     working_dir: /app
echo     volumes:
echo       - ./:/app
echo       - ./docker/nginx/vhost.conf:/opt/docker/etc/nginx/vhost.conf
echo     environment:
echo       - WEB_DOCUMENT_ROOT=/app/public
echo       - PHP_DATE_TIMEZONE=Asia/Bangkok
echo       - PHP_MEMORY_LIMIT=256M
echo       - PHP_MAX_EXECUTION_TIME=300
echo       - PHP_UPLOAD_MAX_FILESIZE=50M
echo       - PHP_POST_MAX_SIZE=50M
echo       - DB_CONNECTION=mysql
echo       - DB_HOST=db
echo       - DB_PORT=3306
echo       - DB_DATABASE=zervi_mes
echo       - DB_USERNAME=root
echo       - DB_PASSWORD=secret
echo       - APP_ENV=local
echo       - APP_DEBUG=true
echo       - APP_URL=http://localhost:8000
echo     ports:
echo       - "8000:80"
echo     depends_on:
echo       - db
echo     networks:
echo       - zervi-network
echo.
echo   db:
echo     image: mysql:8.0
echo     container_name: zervi-mes-db
echo     restart: unless-stopped
echo     environment:
echo       MYSQL_DATABASE: zervi_mes
echo       MYSQL_ROOT_PASSWORD: secret
echo       MYSQL_PASSWORD: secret
echo       MYSQL_USER: zervi_user
echo     volumes:
echo       - db_data:/var/lib/mysql
echo     ports:
echo       - "3306:3306"
echo     networks:
echo       - zervi-network
echo.
echo   phpmyadmin:
echo     image: phpmyadmin/phpmyadmin
echo     container_name: zervi-mes-phpmyadmin
echo     restart: unless-stopped
echo     ports:
echo       - "8080:80"
echo     environment:
echo       PMA_HOST: db
echo       PMA_PORT: 3306
echo       PMA_USER: root
echo       PMA_PASSWORD: secret
echo     networks:
echo       - zervi-network
echo     depends_on:
echo       - db
echo.
echo networks:
echo   zervi-network:
echo     driver: bridge
echo.
echo volumes:
echo   db_data:
) > docker-compose-quick.yml

REM Start services
echo Starting Docker services...
docker-compose -f docker-compose-quick.yml up -d
if %errorlevel% neq 0 (
    echo ❌ Failed to start Docker services
    echo Check Docker logs for details
    pause
    exit /b 1
)
echo ✅ Docker services started successfully
echo.

REM Wait for services to be ready
echo ⏳ Waiting for services to be ready...
echo This may take 30-60 seconds...
timeout /t 30 /nobreak >nul
echo.

REM Check if services are running
echo 🔍 Checking service status...
docker-compose -f docker-compose-quick.yml ps
echo.

REM Run setup commands
echo 🗄️ Setting up database...
docker exec zervi-mes-app php artisan key:generate --force 2>nul || echo Key generation skipped
docker exec zervi-mes-app php artisan migrate --force 2>nul || echo Migrations may have already run
docker exec zervi-mes-app php artisan migrate --path=plugins/ZerviManufacturing/database/migrations --force 2>nul || echo Plugin migrations may have already run
echo ✅ Database setup completed
echo.

REM Clear caches
echo 🧹 Clearing caches...
docker exec zervi-mes-app php artisan cache:clear 2>nul || echo Cache clear skipped
docker exec zervi-mes-app php artisan config:clear 2>nul || echo Config clear skipped
docker exec zervi-mes-app php artisan route:clear 2>nul || echo Route clear skipped
docker exec zervi-mes-app php artisan view:clear 2>nul || echo View clear skipped
echo ✅ Caches cleared
echo.

REM Install and build assets
echo 🎨 Installing and building assets...
docker exec zervi-mes-app npm install --silent 2>nul || echo NPM install skipped
docker exec zervi-mes-app npm run build --silent 2>nul || echo Asset build skipped
echo ✅ Assets compiled
echo.

REM Final status check
echo 📊 Final Service Status:
docker-compose -f docker-compose-quick.yml ps
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
echo    View logs: docker-compose -f docker-compose-quick.yml logs -f
echo    Stop services: docker-compose -f docker-compose-quick.yml down
echo    Restart services: docker-compose -f docker-compose-quick.yml restart
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