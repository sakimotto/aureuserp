<?php

namespace Zervi\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Zervi\Manufacturing\Enums\Department;
use Zervi\Manufacturing\Enums\Priority;
use Zervi\Manufacturing\Enums\WorkOrderStatus;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'zervi_work_orders';
    
    protected $fillable = [
        'wo_number', 'barcode', 'product_id', 'quantity_requested', 'quantity_completed',
        'quantity_scrap', 'current_department', 'status', 'priority', 'planned_start_date',
        'planned_end_date', 'actual_start', 'actual_end', 'sales_order_line_id',
        'promised_delivery_date', 'customer_po_number', 'owning_department_id',
        'assigned_to', 'estimated_material_cost', 'actual_material_cost', 'labor_cost',
        'batch_number', 'lot_number', 'created_by'
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'promised_delivery_date' => 'date',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'current_department' => Department::class,
        'status' => WorkOrderStatus::class,
        'priority' => Priority::class,
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Products\Models\Product::class);
    }

    public function owningDepartment(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Employees\Models\Department::class, 'owning_department_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Employees\Models\Employee::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Security\Models\User::class, 'created_by');
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

    // Supervisor-focused scopes
    public function scopeForSupervisorDashboard($query, $departmentId = null)
    {
        $departmentId = $departmentId ?? auth()->user()->primaryDepartment()?->id;
        
        return $query->where('owning_department_id', $departmentId)
            ->whereIn('status', ['queued', 'in_progress', 'qc_hold'])
            ->with(['product', 'salesOrderLine.salesOrder.customer'])
            ->orderBy('promised_delivery_date', 'asc');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('current_department', $department);
    }

    public function scopeOverdue($query)
    {
        return $query->where('promised_delivery_date', '<', now()->startOfDay())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeWithShortages($query)
    {
        return $query->whereHas('materialLines', function ($q) {
            $q->where('has_shortage', true);
        });
    }

    public function scopeBlocked($query)
    {
        return $query->whereHas('tasks', function ($q) {
            $q->where('status', 'blocked');
        });
    }

    // Business logic for supervisor context
    public function getProgressPercentageAttribute(): float
    {
        if ($this->quantity_requested == 0) return 0;
        return ($this->quantity_completed / $this->quantity_requested) * 100;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->promised_delivery_date && 
               $this->promised_delivery_date < now()->startOfDay() &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getCustomerCommitmentAttribute(): array
    {
        return [
            'customer_name' => $this->salesOrderLine?->salesOrder?->customer?->name ?? 'Stock',
            'po_number' => $this->customer_po_number,
            'delivery_date' => $this->promised_delivery_date,
            'is_overdue' => $this->is_overdue,
        ];
    }

    public function getActiveIssuesAttribute(): array
    {
        return [
            'material_shortage' => $this->materialLines()
                ->where('has_shortage', true)
                ->exists(),
                
            'qc_failed' => $this->qualityRecords()
                ->where('result', 'fail')
                ->where('requires_resolution', true)
                ->exists(),
                
            'blocked_tasks' => $this->tasks()
                ->where('status', 'blocked')
                ->exists(),
                
            'overdue' => $this->is_overdue,
        ];
    }

    // Auto-generate numbers on creation
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($workOrder) {
            if (empty($workOrder->wo_number)) {
                $workOrder->wo_number = 'WO-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
            if (empty($workOrder->barcode)) {
                $workOrder->barcode = 'ZRV' . date('Y') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            }
            if (empty($workOrder->batch_number)) {
                $workOrder->batch_number = 'BATCH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            }
        });
    }
}