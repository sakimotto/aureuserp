# update for departments

**Absolutely agree.**

You're describing a **Manufacturing Execution System (MES)** view—supervisors need an operational dashboard, not an ERP labyrinth. They shouldn't see Sales invoices or Accounting journals. They need **production control**.

## The Tent Supervisor's World (Focused View)

```
┌──────────────────────────────────────────────────────────────┐
│  TENT DEPARTMENT DASHBOARD          [User: Jane | Role: Sup] │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │ ACTIVE JOBS  │  │  TODAY DUE   │  │  BLOCKED     │       │
│  │     12       │  │     3        │  │     1        │       │
│  │   ━━━━       │  │   ⚠️         │  │   🚨         │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
│                                                              │
│  PRODUCTION KANBAN BOARD                                     │
│  ┌──────────┬──────────┬──────────┬──────────┬──────────┐   │
│  │  QUEUED  │ CUTTING  │  SEWING  │   QC     │ COMPLETE │   │
│  │          │  (CNC)   │  (TENT)  │          │          │   │
│  ├──────────┼──────────┼──────────┼──────────┼──────────┤   │
│  │ WO-104   │ WO-103   │ WO-101   │          │ WO-98    │   │
│  │ Toyota   │ Hilux    │ Canvas   │          │ Camping  │   │
│  │ 50pcs    │ 30pcs    │ 100pcs   │          │ 20pcs    │   │
│  │ Due: Fri │ Due: Thu │ Due: Wed │          │ Delivered│   │
│  ├──────────┤          ├──────────┤          │          │   │
│  │ WO-105   │          │ WO-102   │          │          │   │
│  │ Camping  │          │ Tent     │          │          │   │
│  │ 20pcs    │          │ 50pcs    │          │          │   │
│  │ Due: Mon │          │ Due: Thu │          │          │   │
│  └──────────┴──────────┴──────────┴──────────┴──────────┘   │
│                                                              │
│  [Drag card to move]  [+ New MO]  [⚠️ Material Shortage]     │
│                                                              │
│  DELIVERY COMMITMENTS (Next 7 Days)                          │
│  ├─ Thursday: Toyota Hilux Order #TH-240215 (50 covers)      │
│  ├─ Friday:  Camping Tent Lot #CAMP-089 (20 units)           │
│  └─ Monday:  Event Tent Corporate (100 units) ← Material wait│
│                                                              │
└──────────────────────────────────────────────────────────────┘

SIDE NAVIGATION (Only 4 items):
┌─────────────────┐
│ 📊 Dashboard    │ ← Current view
│ 📋 Production   │ ← Kanban + Work Orders
│ 📦 Inventory    │ ← My materials, shortages
│ ⚠️  Issues      │ ← Blocked tasks, QC fails
└─────────────────┘

(Hidden from this user: Sales, Accounting, HR, Purchases)
```

## Implementation in Aureus/Filament

### 1. Role-Based Menu Filtering

```php
// In ServiceProvider
Filament::serving(function () {
    $user = auth()->user();
    
    // Manufacturing Supervisor sees only 4 modules
    if ($user->hasRole('dept_supervisor')) {
        Filament::registerNavigationGroups([
            NavigationGroup::make('Operations')
                ->items([
                    Dashboard::class,
                    WorkOrderResource::class, // With Kanban view
                    InventoryResource::class, // Scoped to dept
                    IssueResource::class,     // Blocked tasks
                ]),
        ]);
        
        // Hide Sales, Accounting, HR from sidebar
        SalesResource::shouldRegisterNavigation(false);
        AccountingResource::shouldRegisterNavigation(false);
    }
});
```

### 2. Department-Scoped Kanban Board

```php
// Add to WorkOrderResource
public static function getPages(): array
{
    return [
        'index' => Pages\ListWorkOrders::route('/'), // Table view
        'kanban' => Pages\WorkOrderKanban::route('/kanban'), // Board view
        // ... other pages
    ];
}

// Kanban Page (custom Filament page)
class WorkOrderKanban extends Page
{
    protected static string $resource = WorkOrderResource::class;
    
    public function getViewData(): array
    {
        $deptId = auth()->user()->primaryDepartment()->id;
        
        return [
            'columns' => [
                'queued' => WorkOrder::byDepartment($deptId)
                    ->where('status', 'queued')
                    ->with('product', 'salesOrder')
                    ->get(),
                    
                'cutting' => WorkOrder::where('current_location_id', $deptId)
                    ->where('current_department', 'cutting')
                    ->get(),
                    
                'sewing' => WorkOrder::byDepartment($deptId)
                    ->where('current_department', 'sewing')
                    ->get(),
                    
                'qc' => WorkOrder::byDepartment($deptId)
                    ->where('status', 'qc_hold')
                    ->get(),
                    
                'complete' => WorkOrder::byDepartment($deptId)
                    ->where('status', 'completed')
                    ->whereDate('completed_at', '>=', now()->subDays(7))
                    ->get(),
            ]
        ];
    }
}
```

### 3. The Kanban Card (Rich Information)

```php
// Blade component for each card
<div class="kanban-card">
    <div class="card-header">
        <span class="wo-number">{{ $workOrder->wo_number }}</span>
        <span class="priority-badge {{ $workOrder->priority }}">
            {{ $workOrder->priority }}
        </span>
    </div>
    
    <div class="product-info">
        <strong>{{ $workOrder->product->name }}</strong>
        <span class="qty">{{ $workOrder->quantity_requested }} pcs</span>
    </div>
    
    <div class="commitment">
        <span class="customer">
            🏢 {{ $workOrder->salesOrder->customer_name ?? 'Internal' }}
        </span>
        <span class="due-date {{ $workOrder->isOverdue() ? 'danger' : '' }}">
            📅 {{ $workOrder->promised_delivery_date->format('M d') }}
        </span>
    </div>
    
    <div class="status-bar">
        <div class="progress" style="width: {{ $workOrder->progressPercentage }}%"></div>
    </div>
    
    <div class="actions">
        <button wire:click="startProduction({{ $workOrder->id }})">
            Start
        </button>
        <button wire:click="viewDetails({{ $workOrder->id }})">
            Details
        </button>
    </div>
</div>
```

### 4. Customer Commitment Visibility

Supervisors need to see **why** this job matters:

```php
// In WorkOrder model
public function salesOrderLine(): BelongsTo
{
    return $this->belongsTo(SalesOrderLine::class);
}

public function customerCommitment(): array
{
    return [
        'customer_name' => $this->salesOrderLine?->salesOrder?->customer?->name ?? 'Stock',
        'po_number' => $this->salesOrderLine?->salesOrder?->po_reference,
        'delivery_date' => $this->salesOrderLine?->promised_delivery_date,
        'penalty_clause' => $this->salesOrderLine?->late_delivery_penalty,
    ];
}
```

### 5. Issue Tracking (What's Blocking Me?)

```php
// Issue flags visible on dashboard
public function getActiveIssuesAttribute()
{
    return [
        'material_shortage' => $this->materialLines()
            ->where('status', 'shortage')
            ->exists(),
            
        'qc_failed' => $this->qualityRecords()
            ->where('result', 'fail')
            ->whereNull('resolved_at')
            ->exists(),
            
        'machine_down' => $this->tasks()
            ->where('status', 'blocked')
            ->where('block_reason', 'machine_maintenance')
            ->exists(),
            
        'overdue' => $this->promised_delivery_date < now(),
    ];
}
```

## The Supervisor's Daily Flow


1. **Login** → Sees Dashboard (not full ERP menu)
2. **Check Kanban** → What's in my department today?
3. **Drag & Drop** → Move WO from "Cutting" to "Sewing" when CNC delivers parts
4. **See Blockers** → Red alerts for material shortages or QC issues
5. **Check Commitments** → "Oh no, Toyota order is due Thursday and we're behind"
6. **Click WO** → See work instructions, drawings, material pick list
7. **Report Issue** → "Need more thread, stopping production"

## Technical Implementation for Trea

**Add to Phase 1 (Department Setup):**

* Create Kanban view for WorkOrderResource
* Add `promised_delivery_date` to Work Orders (from Sales Order)
* Create dashboard widgets (Active Jobs, Overdue, Blocked)

**Add to Work Order Model:**

```php
// Scope for supervisor's view
public function scopeForSupervisorDashboard($query, $departmentId)
{
    return $query->where('owning_department_id', $departmentId)
        ->whereIn('status', ['queued', 'in_progress', 'qc_hold'])
        ->with(['product', 'salesOrderLine.salesOrder.customer']);
}
```

**Yes, this is exactly the right approach.**

Department supervisors are **operators**, not administrators. They need:

* **Visual** (Kanban) not tabular
* **Focused** (their department only) not everything
* **Action-oriented** (Start/Stop/Report Issue) not data entry
* **Customer-aware** (delivery dates) not isolated

Tell Trea to build the **Kanban view first** before the table view. Supervisors will live in that board 8 hours a day.

Should I specify the Kanban drag-and-drop logic for Filament, or the dashboard widget queries?