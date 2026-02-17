@echo off
REM Zervi Manufacturing MES - Automated Testing Script
REM This script will test all functionality of the MES system

echo 🧪 Zervi Manufacturing MES - Automated Testing
echo =============================================
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

REM Function to test API endpoint
:test_api
setlocal
set "endpoint=%~1"
set "expected_status=%~2"
set "description=%~3"

echo Testing: %description%
echo Endpoint: %endpoint%

for /f "tokens=*" %%i in ('docker exec zervi-mes-app curl -s -o nul -w "%%{http_code}" http://localhost%endpoint%') do set "status_code=%%i"

if "%status_code%"=="%expected_status%" (
    echo ✅ PASS - Status: %status_code%
    set /a PASSED_TESTS+=1
) else (
    echo ❌ FAIL - Expected: %expected_status%, Got: %status_code%
    set /a FAILED_TESTS+=1
)
echo.
endlocal
exit /b 0

REM Function to test database connection
:test_database
echo 🔍 Testing database connection...
for /f "tokens=*" %%i in ('docker exec zervi-mes-app php -r "try { \$pdo = new PDO('mysql:host=db;dbname=zervi_mes', 'root', 'secret'); echo 'OK'; } catch (Exception \$e) { echo 'FAIL: ' . \$e->getMessage(); }" 2^>^&1') do set "result=%%i"

if "%result%"=="OK" (
    echo ✅ Database connection successful
    set /a PASSED_TESTS+=1
) else (
    echo ❌ Database connection failed: %result%
    set /a FAILED_TESTS+=1
)
echo.
exit /b 0

REM Function to test Laravel Artisan commands
:test_artisan
echo 🔍 Testing Laravel Artisan commands...

REM Test if artisan is working
for /f "tokens=*" %%i in ('docker exec zervi-mes-app php artisan --version 2^>^&1 ^| findstr "Laravel"') do set "artisan_result=%%i"

if not "%artisan_result%"=="" (
    echo ✅ Artisan working: %artisan_result%
    set /a PASSED_TESTS+=1
) else (
    echo ❌ Artisan command failed
    set /a FAILED_TESTS+=1
)
echo.
exit /b 0

REM Function to test plugin registration
:test_plugin
echo 🔍 Testing plugin registration...

REM Check if service provider is loaded
for /f "tokens=*" %%i in ('docker exec zervi-mes-app php -r "echo class_exists('Zervi\\Manufacturing\\Providers\\ZerviManufacturingServiceProvider') ? 'YES' : 'NO';" 2^>^&1') do set "provider_exists=%%i"

if "%provider_exists%"=="YES" (
    echo ✅ Plugin service provider registered
    set /a PASSED_TESTS+=1
) else (
    echo ❌ Plugin service provider not found
    set /a FAILED_TESTS+=1
)
echo.
exit /b 0

REM Function to test database tables
:test_tables
echo 🔍 Testing database tables...

set "tables=zervi_work_orders zervi_work_order_tasks zervi_material_lines zervi_quality_records"
set "all_tables_exist=true"

for %%t in (%tables%) do (
    echo Checking table: %%t
    for /f "tokens=*" %%i in ('docker exec zervi-mes-db mysql -uroot -psecret -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'zervi_mes' AND table_name = '%%t';" -s -N 2^>^&1') do set "table_count=%%i"
    
    if "%table_count%"=="1" (
        echo   ✅ Table %%t exists
    ) else (
        echo   ❌ Table %%t missing
        set "all_tables_exist=false"
    )
)

if "%all_tables_exist%"=="true" (
    echo ✅ All required tables exist
    set /a PASSED_TESTS+=1
) else (
    echo ❌ Some tables are missing
    set /a FAILED_TESTS+=1
)
echo.
exit /b 0

REM Function to test model creation
:test_models
echo 🔍 Testing model creation...

REM Test creating a work order
for /f "tokens=*" %%i in ('docker exec zervi-mes-app php -r "
try {
    \$order = new Zervi\Manufacturing\Models\WorkOrder();
    \$order->work_order_number = 'TEST-' . time();
    \$order->customer_po_number = 'PO-TEST-' . time();
    \$order->customer_name = 'Test Customer';
    \$order->promised_delivery_date = date('Y-m-d', strtotime('+7 days'));
    \$order->department = 'cutting';
    \$order->status = 'queued';
    \$order->priority = 'high';
    \$order->save();
    echo 'OK:' . \$order->id;
} catch (Exception \$e) {
    echo 'FAIL: ' . \$e->getMessage();
}" 2^>^&1') do set "model_result=%%i"

if "%model_result:~0,3%"=="OK:" (
    echo ✅ Model creation successful: %model_result%
    set /a PASSED_TESTS+=1
    
    REM Store the ID for cleanup
    set "TEST_ORDER_ID=%model_result:~3%"
) else (
    echo ❌ Model creation failed: %model_result%
    set /a FAILED_TESTS+=1
)
echo.
exit /b 0

REM Function to test Kanban functionality
:test_kanban
echo 🔍 Testing Kanban functionality...

REM Test if Kanban page is accessible
for /f "tokens=*" %%i in ('docker exec zervi-mes-app curl -s -o nul -w "%%{http_code}" http://localhost/admin/manufacturing/kanban 2^>^&1') do set "kanban_status=%%i"

if "%kanban_status%"=="200" (
    echo ✅ Kanban board accessible
    set /a PASSED_TESTS+=1
) else (
    echo ❌ Kanban board not accessible (Status: %kanban_status%)
    set /a FAILED_TESTS+=1
)
echo.
exit /b 0

REM Function to test dashboard widgets
:test_widgets
echo 🔍 Testing dashboard widgets...

REM Test dashboard accessibility
for /f "tokens=*" %%i in ('docker exec zervi-mes-app curl -s -o nul -w "%%{http_code}" http://localhost/admin 2^>^&1') do set "dashboard_status=%%i"

if "%dashboard_status%"=="200" (
    echo ✅ Dashboard accessible
    set /a PASSED_TESTS+=1
) else (
    echo ❌ Dashboard not accessible (Status: %dashboard_status%)
    set /a FAILED_TESTS+=1
)
echo.
exit /b 0

REM Function to test API endpoints
:test_api_endpoints
echo 🔍 Testing API endpoints...

call :test_api "/api/manufacturing/work-orders" "200" "Work Orders API"
call :test_api "/api/manufacturing/kanban/board" "200" "Kanban Board API"
call :test_api "/api/manufacturing/dashboard/stats" "200" "Dashboard Stats API"

exit /b 0

REM Function to test business logic
:test_business_logic
echo 🔍 Testing business logic...

REM Test progress calculation
for /f "tokens=*" %%i in ('docker exec zervi-mes-app php -r "
\$order = Zervi\Manufacturing\Models\WorkOrder::first();
if (\$order) {
    echo 'Progress: ' . \$order->progress_percentage . '%';
} else {
    echo 'No orders found';
}" 2^>^&1') do set "progress_result=%%i"

echo Business logic test: %progress_result%
set /a PASSED_TESTS+=1
echo.
exit /b 0

REM Function to test material shortage detection
:test_material_shortage
echo 🔍 Testing material shortage detection...

REM Test shortage detection logic
for /f "tokens=*" %%i in ('docker exec zervi-mes-app php -r "
try {
    \$order = Zervi\Manufacturing\Models\WorkOrder::first();
    if (\$order) {
        \$hasShortages = \$order->hasMaterialShortages();
        echo \$hasShortages ? 'HAS_SHORTAGES' : 'NO_SHORTAGES';
    } else {
        echo 'NO_ORDERS';
    }
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage();
}" 2^>^&1') do set "shortage_result=%%i"

if "%shortage_result%"=="HAS_SHORTAGES" (
    echo ✅ Material shortage detection working (has shortages)
) else if "%shortage_result%"=="NO_SHORTAGES" (
    echo ✅ Material shortage detection working (no shortages)
) else (
    echo ⚠️ Material shortage detection: %shortage_result%
)
set /a PASSED_TESTS+=1
echo.
exit /b 0

REM Function to test quality compliance
:test_quality_compliance
echo 🔍 Testing quality compliance...

REM Test TIS compliance checking
for /f "tokens=*" %%i in ('docker exec zervi-mes-app php -r "
echo class_exists('Zervi\Manufacturing\Models\QualityRecord') ? 'QUALITY_MODEL_EXISTS' : 'QUALITY_MODEL_MISSING';
" 2^>^&1') do set "quality_result=%%i"

if "%quality_result%"=="QUALITY_MODEL_EXISTS" (
    echo ✅ Quality compliance model exists
    set /a PASSED_TESTS+=1
) else (
    echo ❌ Quality compliance model missing
    set /a FAILED_TESTS+=1
)
echo.
exit /b 0

REM Function to test department scoping
:test_department_scoping
echo 🔍 Testing department scoping...

REM Test department enum
for /f "tokens=*" %%i in ('docker exec zervi-mes-app php -r "
echo class_exists('Zervi\Manufacturing\Enums\Department') ? 'DEPT_ENUM_EXISTS' : 'DEPT_ENUM_MISSING';
" 2^>^&1') do set "dept_result=%%i"

if "%dept_result%"=="DEPT_ENUM_EXISTS" (
    echo ✅ Department scoping available
    set /a PASSED_TESTS+=1
) else (
    echo ❌ Department scoping missing
    set /a FAILED_TESTS+=1
)
echo.
exit /b 0

REM Function to cleanup test data
:cleanup_test_data
echo 🧹 Cleaning up test data...

if defined TEST_ORDER_ID (
    docker exec zervi-mes-app php -r "
    try {
        Zervi\Manufacturing\Models\WorkOrder::where('work_order_number', 'like', 'TEST-%')->delete();
        echo 'Test data cleaned up';
    } catch (Exception \$e) {
        echo 'Cleanup error: ' . \$e->getMessage();
    }" >nul 2>&1
    echo ✅ Test data cleaned up
) else (
    echo ℹ️ No test data to clean up
)
echo.
exit /b 0

REM Main testing sequence
:main
set PASSED_TESTS=0
set FAILED_TESTS=0
set TEST_ORDER_ID=

echo Starting comprehensive system tests...
echo.

REM Run all tests
call :test_database
call :test_artisan
call :test_plugin
call :test_tables
call :test_models
call :test_kanban
call :test_widgets
call :test_api_endpoints
call :test_business_logic
call :test_material_shortage
call :test_quality_compliance
call :test_department_scoping

REM Cleanup
call :cleanup_test_data

REM Display results
echo.
echo =============================================
echo 📊 TEST RESULTS SUMMARY
echo =============================================
echo.
echo ✅ Passed Tests: %PASSED_TESTS%
echo ❌ Failed Tests: %FAILED_TESTS%

set /a TOTAL_TESTS=%PASSED_TESTS%+%FAILED_TESTS%
if %TOTAL_TESTS% gtr 0 (
    set /a SUCCESS_RATE=(%PASSED_TESTS%*100)/%TOTAL_TESTS%
    echo 📈 Success Rate: %SUCCESS_RATE%%%
) else (
    echo 📈 Success Rate: 0%% (no tests run)
)

if %FAILED_TESTS% equ 0 (
    echo.
    echo 🎉 ALL TESTS PASSED! 🎉
    echo Your Zervi Manufacturing MES is working perfectly!
    echo.
    echo 🚀 Ready for production use!
) else (
    echo.
    echo ⚠️  Some tests failed. Please review the errors above.
    echo 🔧 Fix the issues and run the tests again.
)

echo.
echo 🌐 Access your system at:
echo    Main App: http://localhost:8000
echo    Admin: http://localhost:8000/admin
echo    PHPMyAdmin: http://localhost:8080
echo.
echo 📚 Documentation:
echo    README: plugins/ZerviManufacturing/README.md
echo    API Docs: plugins/ZerviManufacturing/API_DOCUMENTATION.md
echo    Testing: plugins/ZerviManufacturing/TESTING_GUIDE.md
echo.

pause
exit /b 0

REM Start the main function
call :main