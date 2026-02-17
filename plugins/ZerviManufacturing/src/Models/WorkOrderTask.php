<?php

namespace Zervi\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Zervi\Manufacturing\Enums\Department;
use Zervi\Manufacturing\Enums\TaskStatus;

class WorkOrderTask extends Model
{
    use HasFactory;

    protected $table = 'zervi_work_order_tasks';
    
    protected $fillable = [
        'work_order_id', 'assigned_employee_id', 'operation_name', 'department',
        'sequence_order', 'predecessor_task_id', 'estimated_hours', 'actual_hours',
        'started_at', 'completed_at', 'status', 'is_blocked', 'block_reason',
        'block_notes', 'blocked_at', 'blocked_by', 'requires_qc', 'qc_record_id',
        'work_instructions', 'attachments', 'machine_id', 'tooling_required'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'blocked_at' => 'datetime',
        'status' => TaskStatus::class,
        'department' => Department::class,
        'block_reason' => 'string',
        'attachments' => 'array',
        'is_blocked' => 'boolean',
        'requires_qc' => 'boolean',
    ];

    // Relationships
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Employees\Models\Employee::class, 'assigned_employee_id');
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Security\Models\User::class, 'blocked_by');
    }

    public function qcRecord(): BelongsTo
    {
        return $this->belongsTo(QualityRecord::class, 'qc_record_id');
    }

    public function predecessorTask(): BelongsTo
    {
        return $this->belongsTo(WorkOrderTask::class, 'predecessor_task_id');
    }

    // Supervisor-focused scopes
    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeReadyForWork($query)
    {
        return $query->where('status', 'ready')
            ->where('is_blocked', false);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Business logic
    public function getIsReadyAttribute(): bool
    {
        return $this->status === 'ready' && !$this->is_blocked;
    }

    public function getCanStartAttribute(): bool
    {
        // Check if predecessor is completed
        if ($this->predecessorTask && $this->predecessorTask->status !== 'completed') {
            return false;
        }
        
        return $this->is_ready;
    }

    public function getBlockReasonLabelAttribute(): ?string
    {
        if (!$this->is_blocked || !$this->block_reason) {
            return null;
        }
        
        return match($this->block_reason) {
            'material_shortage' => 'Material Shortage',
            'machine_maintenance' => 'Machine Maintenance',
            'qc_failure' => 'QC Failure',
            'operator_unavailable' => 'Operator Unavailable',
            default => 'Unknown',
        };
    }

    // Actions for supervisors
    public function blockTask(string $reason, string $notes = null, $userId = null): void
    {
        $this->update([
            'is_blocked' => true,
            'block_reason' => $reason,
            'block_notes' => $notes,
            'blocked_at' => now(),
            'blocked_by' => $userId ?? auth()->id(),
            'status' => 'blocked',
        ]);
    }

    public function unblockTask(): void
    {
        $this->update([
            'is_blocked' => false,
            'block_reason' => null,
            'block_notes' => null,
            'blocked_at' => null,
            'blocked_by' => null,
            'status' => 'ready',
        ]);
    }

    public function startTask($userId = null): void
    {
        if (!$this->can_start) {
            throw new \Exception('Task cannot be started');
        }
        
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function completeTask(float $actualHours = null): void
    {
        $this->update([
            'status' => $this->requires_qc ? 'qc_required' : 'completed',
            'completed_at' => now(),
            'actual_hours' => $actualHours ?? $this->estimated_hours,
        ]);
    }
}