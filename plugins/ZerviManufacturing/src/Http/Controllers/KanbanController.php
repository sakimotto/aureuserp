<?php

namespace Zervi\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zervi\Manufacturing\Models\WorkOrder;
use Zervi\Manufacturing\Models\WorkOrderTask;
use Zervi\Manufacturing\Models\MaterialLine;
use Zervi\Manufacturing\Models\QualityRecord;
use Zervi\Manufacturing\Enums\Department;

class KanbanController extends Controller
{
    /**
     * Move work order between departments (drag & drop)
     */
    public function moveWorkOrder(Request $request)
    {
        $request->validate([
            'work_order_id' => 'required|exists:zervi_work_orders,id',
            'from_department' => 'required|string',
            'to_department' => 'required|string',
        ]);

        $workOrder = WorkOrder::find($request->work_order_id);
        
        // Validate department transition
        if (!in_array($request->to_department, Department::values())) {
            return response()->json(['error' => 'Invalid department'], 400);
        }

        $workOrder->update([
            'current_department' => $request->to_department,
            'status' => $this->getStatusForDepartment($request->to_department),
        ]);

        return response()->json([
            'success' => true,
            'work_order' => $workOrder->fresh(),
            'message' => "Work order moved to {$request->to_department}"
        ]);
    }

    /**
     * Start a task (operator action)
     */
    public function startTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:zervi_work_order_tasks,id',
        ]);

        $task = WorkOrderTask::find($request->task_id);
        
        if (!$task->can_start) {
            return response()->json(['error' => 'Task cannot be started'], 400);
        }

        $task->startTask();

        return response()->json([
            'success' => true,
            'task' => $task->fresh(),
            'message' => 'Task started successfully'
        ]);
    }

    /**
     * Complete a task (operator action)
     */
    public function completeTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:zervi_work_order_tasks,id',
            'actual_hours' => 'nullable|numeric|min:0',
        ]);

        $task = WorkOrderTask::find($request->task_id);
        
        if ($task->status !== 'in_progress') {
            return response()->json(['error' => 'Task is not in progress'], 400);
        }

        $task->completeTask($request->actual_hours);

        return response()->json([
            'success' => true,
            'task' => $task->fresh(),
            'message' => 'Task completed successfully'
        ]);
    }

    /**
     * Block a task (report issue)
     */
    public function blockTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:zervi_work_order_tasks,id',
            'block_reason' => 'required|string|in:material_shortage,machine_maintenance,qc_failure,operator_unavailable',
            'block_notes' => 'nullable|string',
        ]);

        $task = WorkOrderTask::find($request->task_id);
        
        $task->blockTask(
            $request->block_reason,
            $request->block_notes,
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'task' => $task->fresh(),
            'message' => 'Task blocked: ' . $task->block_reason_label
        ]);
    }

    /**
     * Unblock a task (resolve issue)
     */
    public function unblockTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:zervi_work_order_tasks,id',
        ]);

        $task = WorkOrderTask::find($request->task_id);
        
        if (!$task->is_blocked) {
            return response()->json(['error' => 'Task is not blocked'], 400);
        }

        $task->unblockTask();

        return response()->json([
            'success' => true,
            'task' => $task->fresh(),
            'message' => 'Task unblocked successfully'
        ]);
    }

    /**
     * Report material shortage
     */
    public function reportShortage(Request $request)
    {
        $request->validate([
            'material_line_id' => 'required|exists:zervi_material_lines,id',
            'shortage_quantity' => 'required|numeric|min:0',
            'expected_restock_date' => 'nullable|date',
            'shortage_notes' => 'nullable|string',
        ]);

        $materialLine = MaterialLine::find($request->material_line_id);
        
        $materialLine->reportShortage(
            $request->shortage_quantity,
            $request->shortage_notes,
            auth()->id()
        );

        if ($request->expected_restock_date) {
            $materialLine->update(['expected_restock_date' => $request->expected_restock_date]);
        }

        return response()->json([
            'success' => true,
            'material_line' => $materialLine->fresh(),
            'message' => 'Material shortage reported'
        ]);
    }

    /**
     * Report QC failure
     */
    public function reportQcFail(Request $request)
    {
        $request->validate([
            'work_order_id' => 'required|exists:zervi_work_orders,id',
            'task_id' => 'nullable|exists:zervi_work_order_tasks,id',
            'description' => 'required|string',
            'defect_quantity' => 'required|integer|min:1',
            'disposition' => 'required|string|in:use_as_is,rework,scrap,return_to_vendor',
        ]);

        $workOrder = WorkOrder::find($request->work_order_id);
        
        $qcRecord = QualityRecord::create([
            'work_order_id' => $workOrder->id,
            'task_id' => $request->task_id,
            'product_id' => $workOrder->product_id,
            'inspected_by' => auth()->user()->employee?->id ?? 1, // Fallback
            'qc_number' => 'QC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'inspection_type' => 'in_process',
            'department' => $workOrder->current_department,
            'result' => 'fail',
            'requires_resolution' => true,
            'fail_description' => $request->description,
            'defect_quantity' => $request->defect_quantity,
            'disposition' => $request->disposition,
            'due_date' => now()->addDays(3), // Resolution due in 3 days
            'inspected_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'qc_record' => $qcRecord,
            'message' => 'QC failure reported - NCR #' . $qcRecord->generateNcrNumber()
        ]);
    }

    /**
     * Get dashboard data for supervisor
     */
    public function getDashboardData(Request $request)
    {
        $departmentId = auth()->user()->primaryDepartment()?->id;
        
        if (!$departmentId) {
            return response()->json(['error' => 'No department assigned'], 400);
        }

        $data = [
            'active_jobs' => WorkOrder::forSupervisorDashboard($departmentId)->count(),
            'overdue_jobs' => WorkOrder::forSupervisorDashboard($departmentId)->overdue()->count(),
            'blocked_tasks' => WorkOrderTask::byDepartment(auth()->user()->primaryDepartment()?->name ?? 'cutting')
                ->blocked()
                ->count(),
            'material_shortages' => MaterialLine::withShortages()
                ->whereHas('workOrder', function ($q) use ($departmentId) {
                    $q->where('owning_department_id', $departmentId);
                })
                ->count(),
            'qc_failures' => QualityRecord::requiresResolution()
                ->byDepartment(auth()->user()->primaryDepartment()?->name ?? 'cutting')
                ->count(),
        ];

        return response()->json($data);
    }

    /**
     * Get status for department transition
     */
    private function getStatusForDepartment(string $department): string
    {
        return match($department) {
            'queued' => 'queued',
            'cutting', 'sewing' => 'in_progress',
            'qc' => 'qc_hold',
            'complete' => 'completed',
            default => 'in_progress',
        };
    }
}