@echo off
REM Zervi Asia Manufacturing MES - Development Server Startup
REM This script starts the Docker development server

echo 🏭 Starting Zervi Asia Manufacturing MES Development Server...
echo ==========================================================
echo.

REM Check if Docker is running
echo 🔍 Checking Docker status...
docker info >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Docker is not running
    echo Please start Docker Desktop and run this script again
    echo.
    echo 🚀 To start Docker Desktop:
    echo 1. Click Start Menu
    echo 2. Search for "Docker Desktop"
    echo 3. Click to start Docker Desktop
    echo 4. Wait for Docker to fully start (whale icon in system tray)
    echo 5. Run this script again
    pause
    exit /b 1
)
echo ✅ Docker is running
echo.

REM Check if our containers are running
echo 🔍 Checking Zervi MES containers...
docker-compose -f docker-compose-quick.yml ps >nul 2>nul
if %errorlevel% neq 0 (
    echo ⚠️  Containers not found, starting services...
    docker-compose -f docker-compose-quick.yml up -d
    if %errorlevel% neq 0 (
        echo ❌ Failed to start containers
        pause
        exit /b 1
    )
    echo ⏳ Waiting for services to start...
    timeout /t 15 /nobreak >nul
) else (
    echo ✅ Containers found, checking status...
)
echo.

REM Verify services are running
echo 🔍 Verifying service status...
docker-compose -f docker-compose-quick.yml ps
echo.

REM Test application access
echo 🧪 Testing application access...
for /f "tokens=*" %%i in ('powershell -Command "(Invoke-WebRequest -Uri http://localhost:8000 -Method GET -UseBasicParsing).StatusCode" 2^>nul') do set "status=%%i"

if "%status%"=="200" (
    echo ✅ Application is responding!
) else (
    echo ⚠️  Application test inconclusive (status: %status%)
)
echo.

echo ==========================================================
echo 🎉 ZERVI ASIA MES DEVELOPMENT SERVER READY!
echo ==========================================================
echo.
echo 🌐 Access URLs:
echo    Main Application: http://localhost:8000
echo    Admin Panel:      http://localhost:8000/admin
echo    Login:            admin@zervi.com / admin123
echo.
echo 📋 Quick Test:
echo 1. Open http://localhost:8000/admin in your browser
echo 2. Login with admin credentials
echo 3. Navigate to Manufacturing → Work Orders
echo 4. See Zervi Asia work orders from Toyota, Isuzu, etc.
echo 5. Test Kanban board drag-and-drop functionality
echo.
echo 🔧 Docker Commands:
echo    View logs:  docker-compose -f docker-compose-quick.yml logs -f
echo    Stop:       docker-compose -f docker-compose-quick.yml down
echo    Restart:    docker-compose -f docker-compose-quick.yml restart
echo.
echo 🎯 Features to Test:
echo • Customer context: "Toyota order due Thursday" vs "WO-104"
echo • Material shortage detection with supplier info
echo • TIS 1238-2564 quality compliance
echo • Supervisor-focused 4-item navigation
echo • Real-time Kanban workflow
echo.
echo 🚀 Happy Manufacturing!
pause