# implementation -plan

**AUREUSERP MANUFACTURING MODULE IMPLEMENTATION PLAN** *Ready for Trae - Start Coding Now*


---

## PHASE 1: FOUNDATION SETUP (Execute First)

### 1. Environment Setup Commands (For Trae)

```bash
# Clone and setup AureusERP
git clone https://github.com/aureuserp/aureuserp.git /var/www/zervi-erp
cd /var/www/zervi-erp

# Install core plugins first
composer install
php artisan aureus:install

# Install required base plugins
php artisan inventories:install    # Stock management
php artisan projects:install       # Project base (we'll extend)
php artisan employees:install      # Operators/departments
php artisan timesheets:install     # Labor tracking
php artisan purchases:install      # Material procurement
php artisan contacts:install       # CRM base

# Create our custom module
php artisan make:plugin ZerviManufacturing
```

### 2. Module Registration

**File**: `/plugins/ZerviManufactanagement/src/Providers/ZerviManufacturingServiceProvider.php`

```php
<?php
namespace Zervi\Manufacturing\Providers;

use Illuminate\Support\ServiceProvider;

class ZerviManufacturingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register module resources
        $this->app->register(FilamentResourceServiceProvider::class);
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }
}
```


---

## PHASE 2: DATABASE SCHEMA (Create These Migration Files)

### Migration 1: Work Orders (Manufacturing Core)

**File**: `/plugins/ZerviManufacturing/database/migrations/2024_02_15_000001_create_work_orders_table.php`

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zervi_work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->string('wo_number')->unique(); // ZERVI-WO-2024-00001
            $table->integer('quantity_requested');
            $table->integer('quantity_completed')->default(0);
            $table->integer('quantity_scrap')->default(0);
            
            // Department routing
            $table->enum('current_department', [
                'cutting', 'lamination', 'sewing_1', 'sewing_2', 
                'sewing_3', 'sewing_4', 'embroidery', 
                'screen_printing', 'airbag', 'packing', 'qc', 'completed'
            ])->default('cutting');
            
            // Status workflow
            $table->enum('status', [
                'draft', 'queued', 'in_progress', 
                'qc_hold', 'completed', 'cancelled'
            ])->default('draft');
            
            // Priority & scheduling
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->timestamp('actual_start')->nullable();
            $table->timestamp('actual_end')->nullable();
            
            // Batch tracking
            $table->string('batch_number')->nullable(); // Internal tracking
            $table->string('lot_number')->nullable();   // Supplier lot
            $table->string('barcode')->unique();
            
            // Material cost tracking (rolled up from material_lines)
            $table->decimal('estimated_material_cost', 12, 2)->default(0);
            $table->decimal('actual_material_cost', 12, 2)->default(0);
            $table->decimal('labor_cost', 12, 2)->default(0);
            
            // Relations
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('employees');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['status', 'current_department']);
            $table->index(['planned_start_date', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zervi_work_orders');
    }
};
```

### Migration 2: Work Order Tasks (Operations)

**File**: `/plugins/ZerviManufacturing/database/migrations/2024_02_15_000002_create_work_order_tasks_table.php`

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zervi_work_order_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('zervi_work_orders')->cascadeOnDelete();
            
            // Operation details
            $table->string('operation_name'); // "Cut Foam Base", "Apply PUR Adhesive"
            $table->enum('department', [
                'cutting', 'lamination', 'sewing', 'embroidery',
                'screen_printing', 'airbag', 'packing', 'qc'
            ]);
            
            // Sequence & dependencies
            $table->integer('sequence_order')->default(0);
            $table->foreignId('predecessor_task_id')->nullable()->constrained('zervi_work_order_tasks');
            
            // Assignment
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees');
            $table->foreignId('machine_id')->nullable(); // Links to your machine inventory
            
            // Time tracking
            $table->decimal('estimated_hours', 8, 2);
            $table->decimal('actual_hours', 8, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Status
            $table->enum('status', [
                'pending', 'ready', 'in_progress', 
                'qc_required', 'completed', 'blocked'
            ])->default('pending');
            
            // Quality checkpoint
            $table->boolean('requires_qc')->default(false);
            $table->foreignId('qc_record_id')->nullable(); // Links to quality module
            
            // Instructions
            $table->text('work_instructions')->nullable();
            $table->json('attachments')->nullable(); // Drawing files, specs
            
            $table->timestamps();
            
            $table->index(['work_order_id', 'sequence_order']);
            $table->index(['assigned_employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zervi_work_order_tasks');
    }
};
```

### Migration 3: Material Lines (MRP)

**File**: `/plugins/ZerviManufacturing/database/migrations/2024_02_15_000003_create_material_lines_table.php`

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zervi_material_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('zervi_work_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products'); // Raw material
            
            // Quantity tracking
            $table->decimal('quantity_required', 10, 3);
            $table->decimal('quantity_reserved', 10, 3)->default(0);
            $table->decimal('quantity_issued', 10, 3)->default(0);
            $table->decimal('quantity_consumed', 10, 3)->default(0);
            $table->decimal('quantity_returned', 10, 3)->default(0);
            
            // Batch/Lot tracking for materials
            $table->string('batch_number')->nullable();
            $table->string('lot_number')->nullable();
            $table->string('supplier_lot')->nullable();
            
            // Source location
            $table->string('source_location')->nullable(); // Warehouse, Bin A-12
            
            // Cost tracking
            $table->decimal('unit_cost', 10, 4);
            $table->decimal('total_cost', 12, 2)->storedAs('quantity_consumed * unit_cost');
            
            // Status
            $table->enum('status', [
                'planned', 'reserved', 'picked', 
                'issued', 'consumed', 'returned'
            ])->default('planned');
            
            // Issuance tracking
            $table->foreignId('picked_by')->nullable()->constrained('employees');
            $table->timestamp('picked_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zervi_material_lines');
    }
};
```

### Migration 4: Quality Records

**File**: `/plugins/ZerviManufacturing/database/migrations/2024_02_15_000004_create_quality_records_table.php`

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zervi_quality_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('zervi_work_orders');
            $table->foreignId('task_id')->nullable()->constrained('zervi_work_order_tasks');
            $table->foreignId('product_id')->constrained('products');
            
            // Inspection details
            $table->string('qc_number')->unique(); // QC-2024-00001
            $table->enum('inspection_type', [
                'incoming', 'in_process', 'final', 'laboratory'
            ]);
            $table->enum('department', [
                'cutting', 'lamination', 'sewing', 'final_qc', 'lab'
            ]);
            
            // Results
            $table->enum('result', ['pass', 'fail', 'conditional', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('measurements')->nullable(); // Specific test data
            
            // Standards compliance
            $table->string('standard_reference')->nullable(); // "TIS 1238-2564"
            $table->json('checklist_results')->nullable(); // Array of check items
            
            // Defect tracking (if failed)
            $table->integer('defect_quantity')->default(0);
            $table->text('defect_description')->nullable();
            $table->enum('disposition', [
                'use_as_is', 'rework', 'scrap', 'return_to_vendor'
            ])->nullable();
            
            // Inspector
            $table->foreignId('inspected_by')->constrained('employees');
            $table->timestamp('inspected_at')->nullable();
            
            // NCR (Non-Conformance Report) link
            $table->string('ncr_number')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zervi_quality_records');
    }
};
```


---

## PHASE 3: FILAMENT RESOURCES (UI Components)

### Resource 1: Work Order Management

**File**: `/plugins/ZerviManufacturing/src/Filament/Resources/WorkOrderResource.php`

```php
<?php
namespace Zervi\Manufacturing\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Zervi\Manufacturing\Models\WorkOrder;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Manufacturing';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Work Order Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('wo_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'WO-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT))
                            ->disabled(),
                            
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        Forms\Components\TextInput::make('quantity_requested')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                            
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal', 
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('normal')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Scheduling')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('planned_start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('planned_end_date')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Tracking')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('batch_number')
                            ->placeholder('Auto-generated if empty'),
                        Forms\Components\TextInput::make('lot_number'),
                        Forms\Components\TextInput::make('barcode')
                            ->unique(ignoreRecord: true),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'queued' => 'Queued',
                                'in_progress' => 'In Progress',
                                'qc_hold' => 'QC Hold',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                            
                        Forms\Components\Select::make('current_department')
                            ->label('Current Department')
                            ->options([
                                'cutting' => 'CNC Cutting',
                                'lamination' => 'PUR Lamination',
                                'sewing_1' => 'Sewing Line 1',
                                'sewing_2' => 'Sewing Line 2',
                                'sewing_3' => 'Sewing Line 3',
                                'sewing_4' => 'Sewing Line 4',
                                'embroidery' => 'Embroidery',
                                'screen_printing' => 'Screen Printing',
                                'airbag' => 'Airbag Assembly',
                                'packing' => 'Packing',
                                'qc' => 'Quality Control',
                                'completed' => 'Completed',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wo_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity_requested')
                    ->numeric(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => ['draft', 'qc_hold'],
                        'success' => 'completed',
                        'primary' => 'in_progress',
                        'secondary' => ['queued', 'draft'],
                    ]),
                Tables\Columns\BadgeColumn::make('current_department')
                    ->label('Department'),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'normal' => 'success',
                        'low' => 'gray',
                    }),
                Tables\Columns\ProgressBarColumn::make('progress')
                    ->getStateUsing(fn ($record) => 
                        $record->quantity_requested > 0 
                            ? ($record->quantity_completed / $record->quantity_requested) * 100 
                            : 0
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('current_department')
                    ->label('Department'),
                Tables\Filters\SelectFilter::make('priority'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print_job_card')
                    ->label('Print Job Card')
                    ->icon('heroicon-m-printer')
                    ->url(fn ($record) => route('work-orders.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('start_production')
                    ->label('Start')
                    ->icon('heroicon-m-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'queued')
                    ->action(fn ($record) => $record->update([
                        'status' => 'in_progress',
                        'actual_start' => now(),
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TasksRelationManager::class,
            RelationManagers\MaterialLinesRelationManager::class,
            RelationManagers\QualityRecordsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
```

### Relation Manager: Tasks (Inside Work Order)

**File**: `/plugins/ZerviManufacturing/src/Filament/Resources/WorkOrderResource/RelationManagers/TasksRelationManager.php`

```php
<?php
namespace Zervi\Manufacturing\Filament\Resources\WorkOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';
    protected static ?string $title = 'Operations';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('operation_name')
                ->required(),
            Forms\Components\Select::make('department')
                ->options([
                    'cutting' => 'CNC Cutting',
                    'lamination' => 'PUR Lamination',
                    'sewing' => 'Sewing',
                    'embroidery' => 'Embroidery',
                    'screen_printing' => 'Screen Printing',
                    'airbag' => 'Airbag',
                    'packing' => 'Packing',
                    'qc' => 'Quality Control',
                ])
                ->required(),
            Forms\Components\TextInput::make('sequence_order')
                ->numeric()
                ->default(0),
            Forms\Components\Select::make('assigned_employee_id')
                ->relationship('assignedEmployee', 'name')
                ->searchable(),
            Forms\Components\TextInput::make('estimated_hours')
                ->numeric()
                ->step(0.5),
            Forms\Components\Toggle::make('requires_qc')
                ->label('Requires Quality Check'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sequence_order')->sortable(),
                Tables\Columns\TextColumn::make('operation_name'),
                Tables\Columns\BadgeColumn::make('department'),
                Tables\Columns\TextColumn::make('assignedEmployee.name')
                    ->label('Operator'),
                Tables\Columns\TextColumn::make('estimated_hours'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'blocked',
                        'warning' => ['pending', 'qc_required'],
                        'success' => 'completed',
                        'primary' => 'in_progress',
                    ]),
            ])
            ->reorderable('sequence_order')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('start')
                    ->icon('heroicon-m-play')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'ready')
                    ->action(fn ($record) => $record->update([
                        'status' => 'in_progress',
                        'started_at' => now(),
                    ])),
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'in_progress')
                    ->form([
                        Forms\Components\TextInput::make('actual_hours')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => $record->requires_qc ? 'qc_required' : 'completed',
                            'completed_at' => now(),
                            'actual_hours' => $data['actual_hours'],
                        ]);
                    }),
            ]);
    }
}
```


---

## PHASE 4: MODELS (Business Logic)

### WorkOrder Model

**File**: `/plugins/ZerviManufacturing/src/Models/WorkOrder.php`

```php
<?php
namespace Zervi\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'zervi_work_orders';
    
    protected $fillable = [
        'project_id', 'product_id', 'wo_number', 'quantity_requested',
        'quantity_completed', 'quantity_scrap', 'current_department',
        'status', 'priority', 'planned_start_date', 'planned_end_date',
        'actual_start', 'actual_end', 'batch_number', 'lot_number',
        'barcode', 'estimated_material_cost', 'actual_material_cost',
        'labor_cost', 'created_by', 'assigned_to'
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(WorkOrderTask::class)->orderBy('sequence_order');
    }

    public function materialLines(): HasMany
    {
        return $this->hasMany(MaterialLine::class);
    }

    public function qualityRecords(): HasMany
    {
        return $this->hasMany(QualityRecord::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['queued', 'in_progress', 'qc_hold']);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('current_department', $department);
    }

    // Business Logic
    public function getProgressPercentageAttribute(): float
    {
        if ($this->quantity_requested == 0) return 0;
        return ($this->quantity_completed / $this->quantity_requested) * 100;
    }

    public function getTotalCostAttribute(): float
    {
        return $this->actual_material_cost + $this->labor_cost;
    }

    // Auto-generate batch number on creation
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($workOrder) {
            if (empty($workOrder->batch_number)) {
                $workOrder->batch_number = 'BATCH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            }
            if (empty($workOrder->barcode)) {
                $workOrder->barcode = 'ZRV' . date('Y') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
```


---

## PHASE 5: BARCODE INTEGRATION (Shop Floor)

### Barcode Scanning Endpoint

**File**: `/plugins/ZerviManufacturing/routes/web.php`

```php
<?php
use Illuminate\Support\Facades\Route;
use Zervi\Manufacturing\Http\Controllers\BarcodeController;

Route::middleware(['auth'])->prefix('manufacturing')->group(function () {
    Route::post('/scan', [BarcodeController::class, 'processScan'])->name('manufacturing.scan');
    Route::get('/shop-floor', [BarcodeController::class, 'shopFloor'])->name('manufacturing.shop-floor');
});
```

### Barcode Controller

**File**: `/plugins/ZerviManufacturing/src/Http/Controllers/BarcodeController.php`

```php
<?php
namespace Zervi\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zervi\Manufacturing\Models\WorkOrder;
use Zervi\Manufacturing\Models\WorkOrderTask;

class BarcodeController extends Controller
{
    public function processScan(Request $request)
    {
        $barcode = $request->input('barcode');
        $action = $request->input('action'); // 'start', 'complete', 'qc_pass'
        
        // Find work order by barcode
        $workOrder = WorkOrder::where('barcode', $barcode)->first();
        
        if (!$workOrder) {
            return response()->json(['error' => 'Work order not found'], 404);
        }
        
        switch ($action) {
            case 'start':
                $workOrder->update([
                    'status' => 'in_progress',
                    'actual_start' => now(),
                ]);
                return response()->json([
                    'success' => true, 
                    'message' => 'Work order started',
                    'work_order' => $workOrder
                ]);
                
            case 'complete':
                // Logic to mark current task complete and move to next
                $currentTask = $workOrder->tasks()
                    ->where('status', 'in_progress')
                    ->first();
                    
                if ($currentTask) {
                    $currentTask->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);
                }
                
                return response()->json(['success' => true]);
                
            default:
                return response()->json(['work_order' => $workOrder]);
        }
    }
    
    public function shopFloor()
    {
        return view('zervi-manufacturing::shop-floor');
    }
}
```


---

## IMMEDIATE NEXT STEPS FOR TRAE


1. **Execute Phase 1**: Setup AureusERP with base plugins
2. **Create Migrations**: Run `php artisan make:migration` for the 4 tables above
3. **Create Models**: WorkOrder, WorkOrderTask, MaterialLine, QualityRecord
4. **Create Filament Resources**: WorkOrderResource with relation managers
5. **Test**: Create one work order, add tasks, verify relation managers work

**After this foundation is solid**, we add:

* Quality module with TIS 1238-2564 compliance
* PUR lamination humidity tracking
* CRM module (Leads/Quotes)
* Shop floor barcode interface

**Start with Work Order CRUD. Verify it works. Then proceed.**

Ready to code?