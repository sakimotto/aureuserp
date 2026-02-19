@echo off
REM Zervi Asia MES - Manual Development Server Startup
REM Alternative method if Docker Desktop has issues

echo 🏭 Zervi Asia Manufacturing MES - Manual Startup
echo ==================================================
echo.

REM Check if Docker is available
echo 🔍 Checking Docker availability...
docker --version >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Docker not found
    echo Please install Docker Desktop from: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)
echo ✅ Docker found
echo.

REM Try to start services directly
echo 🚀 Starting Zervi Asia MES services...
echo.

REM Start MySQL database
echo 📊 Starting MySQL database...
docker run -d --name zervi-mes-db -e MYSQL_ROOT_PASSWORD=secret -e MYSQL_DATABASE=zervi_mes -p 3306:3306 mysql:8.0 >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ MySQL database started
) else (
    echo ⚠️  MySQL container may already exist or failed
)

REM Wait for database to be ready
echo ⏳ Waiting for database...
timeout /t 10 /nobreak >nul

REM Start PHP application
echo 🐘 Starting PHP application...
docker run -d --name zervi-mes-app --link zervi-mes-db:db -p 8000:80 -v %cd%:/app -w /app webdevops/php-nginx:8.2-alpine >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ PHP application started
) else (
    echo ⚠️  PHP container may already exist or failed
)

REM Wait for application to start
echo ⏳ Waiting for application...
timeout /t 15 /nobreak >nul

REM Check service status
echo 🔍 Checking service status...
docker ps | findstr zervi-mes

REM Test application
echo 🧪 Testing application...
curl -s http://localhost:8000 >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ Application is responding!
) else (
    echo ⚠️  Application test inconclusive
)

echo.
echo ==================================================
echo 🎉 MANUAL STARTUP COMPLETE!
echo ==================================================
echo.
echo 🌐 Access URLs:
echo    Main Application: http://localhost:8000
echo    Admin Panel:      http://localhost:8000/admin
echo    Login:            admin@zervi.com / admin123
echo.
echo 📋 Next Steps:
echo 1. Open http://localhost:8000/admin in your browser
echo 2. Login with admin credentials
echo 3. Navigate to Manufacturing → Work Orders
echo 4. Test Zervi Asia features (Toyota, Isuzu, etc.)
echo.
echo 🔧 Container Management:
echo    View logs:  docker logs zervi-mes-app
echo    Stop all:   docker stop zervi-mes-app zervi-mes-db
echo    Remove:     docker rm zervi-mes-app zervi-mes-db
echo.
echo 🚀 Happy Manufacturing!
pause