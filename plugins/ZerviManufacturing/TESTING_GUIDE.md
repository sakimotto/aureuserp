# Zervi Manufacturing MES - Testing & Verification Guide

## 🧪 System Testing Procedures

This guide will help you test the Zervi Manufacturing MES system locally to ensure everything is working correctly.

## 📋 Pre-Testing Checklist

Before running tests, ensure you have:

### ✅ Prerequisites Installed
- [ ] **PHP 8.2+** with required extensions
- [ ] **Composer** (PHP package manager)
- [ ] **Node.js 16+** (for frontend assets)
- [ ] **MySQL 8.0+** or **PostgreSQL 13+**
- [ ] **Git** (for version control)

### ✅ Environment Setup
- [ ] **Database configured** in `.env` file
- [ ] **Migrations run** successfully
- [ ] **Dependencies installed** (`composer install`, `npm install`)
- [ ] **Assets compiled** (`npm run dev` or `npm run build`)

## 🚀 Quick System Test

### Step 1: Run System Test Script
```bash
# Navigate to the plugin directory
cd plugins/ZerviManufacturing

# Run the comprehensive system test
php test-mes-system.php
```

This will test:
- Database schema integrity
- Model relationships
- Business logic functionality
- Kanban workflow
- Material shortage detection
- Quality compliance
- Department scoping

### Step 2: Manual Database Verification
```bash
# Check if tables exist
php artisan tinker
>>> Schema::hasTable('zervi_work_orders')
>>> Schema::hasTable('zervi_work_order_tasks')
>>> Schema::hasTable('zervi_material_lines')
>>> Schema::hasTable('zervi_quality_records')
```

### Step 3: Test Plugin Registration
```bash
# Check if plugin is registered
php artisan route:list | grep manufacturing

# Check service providers
php artisan tinker
>>> app()->getLoadedProviders()
```

## 🔍 Detailed Testing Procedures

### 1. Database Testing

#### Test Table Structure
```sql
-- Check work orders table structure
DESCRIBE zervi_work_orders;

-- Verify expected columns exist
SHOW COLUMNS FROM zervi_work_orders LIKE 'customer_po_number';
SHOW COLUMNS FROM zervi_work_orders LIKE 'promised_delivery_date';
SHOW COLUMNS FROM zervi_work_orders LIKE 'department';
```

#### Test Data Integrity
```php
// Test in tinker
>>> $order = new \Zervi\Manufacturing\Models\WorkOrder();
>>> $order->fill([
    'work_order_number' => 'WO-TEST-001',
    'customer_po_number' => 'PO-TOYOTA-001',
    'customer_name' => 'Toyota Test Customer',
    'promised_delivery_date' => now()->addDays(7),
    'department' => \Zervi\Manufacturing\Enums\Department::CUTTING,
    'status' => \Zervi\Manufacturing\Enums\WorkOrderStatus::QUEUED,
    'priority' => \Zervi\Manufacturing\Enums\Priority::HIGH
]);
>>> $order->save();
```

### 2. Kanban Board Testing

#### Test Visual Components
1. **Access the Kanban board** at: `http://localhost:8000/admin/manufacturing/kanban`
2. **Verify columns display**: QUEUED, CUTTING, SEWING, QC, COMPLETE
3. **Test drag-and-drop**: Move work orders between columns
4. **Check card content**: Customer names, delivery dates, priority badges

#### Test API Endpoints
```bash
# Get Kanban board data
curl -X GET "http://localhost:8000/api/manufacturing/kanban/board" \
  -H "Authorization: Bearer YOUR_API_TOKEN"

# Test task movement
curl -X POST "http://localhost:8000/api/manufacturing/kanban/move-task" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "work_order_id": 1,
    "from_department": "queued",
    "to_department": "cutting",
    "user_id": 1
  }'
```

### 3. Material Shortage Testing

#### Create Test Shortage Scenario
```php
// Create work order with material shortage
>>> $order = \Zervi\Manufacturing\Models\WorkOrder::create([
    'work_order_number' => 'WO-SHORTAGE-001',
    'customer_po_number' => 'PO-SHORTAGE-001',
    'customer_name' => 'Shortage Test Customer',
    'promised_delivery_date' => now()->addDays(5),
    'department' => \Zervi\Manufacturing\Enums\Department::CUTTING,
    'status' => \Zervi\Manufacturing\Enums\WorkOrderStatus::QUEUED,
    'priority' => \Zervi\Manufacturing\Enums\Priority::URGENT
]);

// Create material line with shortage
>>> $material = \Zervi\Manufacturing\Models\MaterialLine::create([
    'work_order_id' => $order->id,
    'product_name' => 'Test Fabric',
    'required_quantity' => 100,
    'available_quantity' => 30,
    'unit' => 'meters',
    'expected_restock_date' => now()->addDays(3)
]);
```

#### Verify Shortage Detection
```php
// Check shortage detection
>>> $order->hasMaterialShortages()
>>> $order->getMaterialShortages()->count()
>>> $order->canProceedWithProduction()
>>> $order->getTotalShortageCost()
```

### 4. Quality Compliance Testing

#### Test TIS Compliance
```php
// Create quality record
>>> $quality = \Zervi\Manufacturing\Models\QualityRecord::create([
    'work_order_id' => $order->id,
    'checkpoint' => 'fabric_inspection',
    'status' => 'passed',
    'inspector_id' => 1,
    'inspection_date' => now(),
    'notes' => 'Fabric meets TIS 1238-2564 specifications'
]);

// Test compliance checking
>>> $order->isTISCompliant()
>>> $order->getPendingQualityChecks()
>>> $order->getQualityCheckpoints()
```

### 5. Department Scoping Testing

#### Test Supervisor Filtering
```php
// Test department scoping
>>> \Zervi\Manufacturing\Models\WorkOrder::forDepartment(\Zervi\Manufacturing\Enums\Department::CUTTING)->get()
>>> \Zervi\Manufacturing\Models\WorkOrder::forDepartment(\Zervi\Manufacturing\Enums\Department::SEWING)->get()

// Test user assignment
>>> $user = auth()->user();
>>> $user->department = \Zervi\Manufacturing\Enums\Department::CUTTING;
>>> $user->save();
```

### 6. Dashboard Widgets Testing

#### Test Widget Data
Access these URLs to test dashboard widgets:
- `http://localhost:8000/admin` (main dashboard)
- Check widget data in browser console

#### Test Widget Queries
```php
// Test widget data generation
>>> \Zervi\Manufacturing\Filament\Widgets\ActiveJobsWidget::getData()
>>> \Zervi\Manufacturing\Filament\Widgets\OverdueJobsWidget::getData()
>>> \Zervi\Manufacturing\Filament\Widgets\MaterialShortagesWidget::getData()
>>> \Zervi\Manufacturing\Filament\Widgets\BlockedTasksWidget::getData()
```

## 🎯 Functional Testing Scenarios

### Scenario 1: Complete Work Order Flow
1. **Create work order** with customer details
2. **Add tasks** for each department
3. **Add materials** (some with shortages)
4. **Move through Kanban** columns
5. **Complete quality checks**
6. **Mark as complete**

### Scenario 2: Material Shortage Handling
1. **Create work order** requiring materials
2. **Set available quantity** less than required
3. **Verify shortage detection**
4. **Set restock date**
5. **Check if production can proceed**

### Scenario 3: Quality Compliance
1. **Create work order** for tent production
2. **Add quality checkpoints** per TIS 1238-2564
3. **Complete inspections**
4. **Verify compliance status**

### Scenario 4: Department Scoping
1. **Login as supervisor** assigned to CUTTING
2. **Verify only cutting orders** are visible
3. **Test navigation filtering**
4. **Check other departments** are hidden

## 📊 Performance Testing

### Test Database Performance
```sql
-- Check query performance
EXPLAIN SELECT * FROM zervi_work_orders WHERE department = 'cutting';
EXPLAIN SELECT * FROM zervi_work_order_tasks WHERE work_order_id = 1;

-- Check index usage
SHOW INDEX FROM zervi_work_orders;
```

### Test API Performance
```bash
# Test API response times
curl -w "@curl-format.txt" -o /dev/null -s "http://localhost:8000/api/manufacturing/work-orders"

# Format file (curl-format.txt):
# time_namelookup:  %{time_namelookup}\n
# time_connect:  %{time_connect}\n
# time_appconnect:  %{time_appconnect}\n
# time_pretransfer:  %{time_pretransfer}\n
# time_redirect:  %{time_redirect}\n
# time_starttransfer:  %{time_starttransfer}\n
# time_total:  %{time_total}\n
```

## 🔧 Common Issues and Solutions

### Issue 1: Tables Not Found
**Solution:**
```bash
# Run migrations
php artisan migrate
php artisan migrate --path=plugins/ZerviManufacturing/database/migrations

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Issue 2: Plugin Not Loading
**Solution:**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-register plugins
php artisan package:discover
```

### Issue 3: Kanban Board Not Displaying
**Solution:**
```bash
# Check routes
php artisan route:list | grep manufacturing

# Check if assets are compiled
npm run dev
# or
npm run build
```

### Issue 4: Database Connection Issues
**Solution:**
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check .env configuration
php artisan config:cache
```

## 📈 Success Criteria

### ✅ System is Ready When:
- [ ] All database tables exist and have correct structure
- [ ] Models can be created and saved successfully
- [ ] Relationships work correctly
- [ ] Kanban board displays and drag-drop works
- [ ] Material shortages are detected properly
- [ ] Quality compliance checks function
- [ ] Department scoping filters correctly
- [ ] API endpoints return expected data
- [ ] Dashboard widgets show data
- [ ] No critical errors in logs

### 🎉 Performance Targets:
- **Page Load Time**: < 2 seconds
- **API Response Time**: < 500ms
- **Database Queries**: < 100ms each
- **Kanban Drag-Drop**: Instant response

## 🚀 Next Steps After Testing

1. **Create Sample Data** for demonstration
2. **Train Users** on new system
3. **Set Up Production Environment**
4. **Configure Backups**
5. **Monitor Performance**

---

**Happy Testing! 🧪**

If you encounter any issues, check the documentation files or create an issue on GitHub.