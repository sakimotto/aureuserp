<?php

namespace Zervi\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialLine extends Model
{
    use HasFactory;

    protected $table = 'zervi_material_lines';
    
    protected $fillable = [
        'work_order_id', 'product_id', 'quantity_required', 'quantity_reserved',
        'quantity_issued', 'quantity_consumed', 'quantity_returned', 'quantity_shortage',
        'has_shortage', 'expected_restock_date', 'shortage_notes', 'shortage_reported_at',
        'shortage_reported_by', 'batch_number', 'lot_number', 'supplier_lot',
        'location_id', 'bin_location', 'unit_cost', 'status', 'picked_by',
        'picked_at', 'issued_by', 'issued_at'
    ];

    protected $casts = [
        'has_shortage' => 'boolean',
        'expected_restock_date' => 'date',
        'shortage_reported_at' => 'datetime',
        'picked_at' => 'datetime',
        'issued_at' => 'datetime',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:2',
    ];

    // Relationships
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Products\Models\Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Inventories\Models\Location::class, 'location_id');
    }

    public function pickedBy(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Employees\Models\Employee::class, 'picked_by');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Employees\Models\Employee::class, 'issued_by');
    }

    public function shortageReportedBy(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Security\Models\User::class, 'shortage_reported_by');
    }

    // Supervisor-focused scopes
    public function scopeWithShortages($query)
    {
        return $query->where('has_shortage', true);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByWorkOrder($query, $workOrderId)
    {
        return $query->where('work_order_id', $workOrderId);
    }

    // Business logic
    public function getAvailableQuantityAttribute(): float
    {
        return $this->quantity_issued - $this->quantity_consumed - $this->quantity_returned;
    }

    public function getIsFullyConsumedAttribute(): bool
    {
        return $this->quantity_consumed >= $this->quantity_required;
    }

    public function getShortagePercentageAttribute(): float
    {
        if ($this->quantity_required == 0) return 0;
        return ($this->quantity_shortage / $this->quantity_required) * 100;
    }

    // Actions for supervisors
    public function reportShortage(float $shortageQuantity, string $notes = null, $userId = null): void
    {
        $this->update([
            'has_shortage' => true,
            'quantity_shortage' => $shortageQuantity,
            'shortage_notes' => $notes,
            'shortage_reported_at' => now(),
            'shortage_reported_by' => $userId ?? auth()->id(),
        ]);
    }

    public function resolveShortage(): void
    {
        $this->update([
            'has_shortage' => false,
            'quantity_shortage' => 0,
            'shortage_notes' => null,
            'shortage_reported_at' => null,
            'shortage_reported_by' => null,
        ]);
    }

    public function pickMaterial($employeeId = null): void
    {
        $this->update([
            'status' => 'picked',
            'picked_by' => $employeeId ?? auth()->user()->employee?->id,
            'picked_at' => now(),
        ]);
    }

    public function issueMaterial(float $quantity, $employeeId = null): void
    {
        $newIssued = $this->quantity_issued + $quantity;
        
        $this->update([
            'quantity_issued' => $newIssued,
            'status' => $newIssued >= $this->quantity_required ? 'issued' : 'picked',
            'issued_by' => $employeeId ?? auth()->user()->employee?->id,
            'issued_at' => now(),
        ]);
    }

    public function consumeMaterial(float $quantity): void
    {
        $newConsumed = $this->quantity_consumed + $quantity;
        
        $this->update([
            'quantity_consumed' => $newConsumed,
            'status' => $newConsumed >= $this->quantity_required ? 'consumed' : 'issued',
        ]);
    }
}