<?php

namespace Zervi\Manufacturing\Filament\Pages;

use Filament\Pages\Page;
use Zervi\Manufacturing\Models\WorkOrder;
use Zervi\Manufacturing\Enums\Department;
use Livewire\Attributes\On;

class WorkOrderKanban extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static string $view = 'zervi-manufacturing::kanban';
    protected static ?string $title = 'Production Board';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 2;

    public $columns = [];
    public $draggedWorkOrder = null;

    public function mount(): void
    {
        $this->loadKanbanData();
    }

    public function loadKanbanData(): void
    {
        $departmentId = auth()->user()->primaryDepartment()?->id;
        
        if (!$departmentId) {
            $this->columns = [];
            return;
        }

        $this->columns = [
            'queued' => [
                'title' => 'Queued',
                'color' => 'gray',
                'work_orders' => WorkOrder::forSupervisorDashboard($departmentId)
                    ->where('current_department', 'queued')
                    ->with(['product', 'salesOrderLine.salesOrder.customer'])
                    ->get(),
            ],
            'cutting' => [
                'title' => 'Cutting (CNC)',
                'color' => 'blue',
                'work_orders' => WorkOrder::forSupervisorDashboard($departmentId)
                    ->where('current_department', 'cutting')
                    ->with(['product', 'salesOrderLine.salesOrder.customer'])
                    ->get(),
            ],
            'sewing' => [
                'title' => 'Sewing (Tent)',
                'color' => 'green',
                'work_orders' => WorkOrder::forSupervisorDashboard($departmentId)
                    ->where('current_department', 'sewing')
                    ->with(['product', 'salesOrderLine.salesOrder.customer'])
                    ->get(),
            ],
            'qc' => [
                'title' => 'Quality Control',
                'color' => 'yellow',
                'work_orders' => WorkOrder::forSupervisorDashboard($departmentId)
                    ->where('current_department', 'qc')
                    ->with(['product', 'salesOrderLine.salesOrder.customer'])
                    ->get(),
            ],
            'complete' => [
                'title' => 'Complete',
                'color' => 'success',
                'work_orders' => WorkOrder::forSupervisorDashboard($departmentId)
                    ->where('current_department', 'complete')
                    ->where('status', 'completed')
                    ->whereDate('completed_at', '>=', now()->subDays(7))
                    ->with(['product', 'salesOrderLine.salesOrder.customer'])
                    ->get(),
            ],
        ];
    }

    #[On('work-order-moved')]
    public function handleWorkOrderMoved($workOrderId, $fromColumn, $toColumn): void
    {
        $workOrder = WorkOrder::find($workOrderId);
        
        if (!$workOrder) {
            return;
        }

        // Validate department transition
        if (!in_array($toColumn, array_keys($this->columns))) {
            return;
        }

        // Update work order
        $workOrder->update([
            'current_department' => $toColumn,
            'status' => $this->getStatusForDepartment($toColumn),
        ]);

        // Reload data
        $this->loadKanbanData();

        $this->dispatch('kanban-updated');
    }

    public function viewWorkOrderDetails($workOrderId): void
    {
        $this->redirect(route('filament.admin.resources.zervi-work-orders.edit', $workOrderId));
    }

    public function startWorkOrder($workOrderId): void
    {
        $workOrder = WorkOrder::find($workOrderId);
        
        if ($workOrder && $workOrder->status === 'queued') {
            $workOrder->update([
                'status' => 'in_progress',
                'actual_start' => now(),
            ]);
            
            $this->loadKanbanData();
            $this->dispatch('work-order-started');
        }
    }

    public function getHeaderWidgets(): array
    {
        return [
            \Zervi\Manufacturing\Filament\Widgets\ActiveJobsWidget::class,
            \Zervi\Manufacturing\Filament\Widgets\OverdueJobsWidget::class,
            \Zervi\Manufacturing\Filament\Widgets\BlockedTasksWidget::class,
            \Zervi\Manufacturing\Filament\Widgets\MaterialShortagesWidget::class,
        ];
    }

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