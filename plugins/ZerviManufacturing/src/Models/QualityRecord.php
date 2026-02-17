<?php

namespace Zervi\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Zervi\Manufacturing\Enums\Department;

class QualityRecord extends Model
{
    use HasFactory;

    protected $table = 'zervi_quality_records';
    
    protected $fillable = [
        'work_order_id', 'task_id', 'product_id', 'inspected_by', 'qc_number',
        'inspection_type', 'department', 'result', 'notes', 'measurements',
        'requires_resolution', 'fail_description', 'disposition', 'defect_quantity',
        'resolved_at', 'resolved_by', 'resolution_notes', 'resolution_action',
        'standard_reference', 'checklist_results', 'ncr_number', 'ncr_status',
        'inspected_at', 'due_date'
    ];

    protected $casts = [
        'inspected_at' => 'datetime',
        'resolved_at' => 'datetime',
        'due_date' => 'datetime',
        'measurements' => 'array',
        'checklist_results' => 'array',
        'requires_resolution' => 'boolean',
        'defect_quantity' => 'integer',
        'department' => Department::class,
    ];

    // Relationships
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(WorkOrderTask::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Products\Models\Product::class);
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Employees\Models\Employee::class, 'inspected_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Security\Models\User::class, 'resolved_by');
    }

    // Supervisor-focused scopes
    public function scopeRequiresResolution($query)
    {
        return $query->where('requires_resolution', true)
            ->whereNull('resolved_at');
    }

    public function scopeOverdueResolution($query)
    {
        return $query->where('requires_resolution', true)
            ->whereNull('resolved_at')
            ->where('due_date', '<', now());
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeFailedInspections($query)
    {
        return $query->where('result', 'fail');
    }

    // Business logic
    public function getIsOverdueAttribute(): bool
    {
        return $this->requires_resolution && 
               $this->due_date && 
               $this->due_date < now() &&
               !$this->resolved_at;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->resolved_at) {
            return 'Resolved';
        }
        
        if ($this->requires_resolution) {
            return $this->is_overdue ? 'Overdue' : 'Pending Resolution';
        }
        
        return ucfirst($this->result);
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->resolved_at) {
            return 'success';
        }
        
        if ($this->requires_resolution) {
            return $this->is_overdue ? 'danger' : 'warning';
        }
        
        return match($this->result) {
            'pass' => 'success',
            'fail' => 'danger',
            'conditional' => 'warning',
            default => 'gray',
        };
    }

    // Actions for supervisors
    public function markAsFailed(string $description, int $defectQuantity = 0, string $disposition = null): void
    {
        $this->update([
            'result' => 'fail',
            'requires_resolution' => true,
            'fail_description' => $description,
            'defect_quantity' => $defectQuantity,
            'disposition' => $disposition,
            'due_date' => now()->addDays(3), // Resolution due in 3 days
        ]);
    }

    public function resolve(string $action, string $notes = null, $userId = null): void
    {
        $this->update([
            'resolved_at' => now(),
            'resolved_by' => $userId ?? auth()->id(),
            'resolution_notes' => $notes,
            'resolution_action' => $action,
            'requires_resolution' => false,
        ]);
    }

    public function generateNcrNumber(): string
    {
        $this->ncr_number = 'NCR-' . date('Y') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
        $this->save();
        
        return $this->ncr_number;
    }
}