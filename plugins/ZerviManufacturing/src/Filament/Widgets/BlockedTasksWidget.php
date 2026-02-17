<?php

namespace Zervi\Manufacturing\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Zervi\Manufacturing\Models\WorkOrderTask;
use Zervi\Manufacturing\Models\MaterialLine;

class BlockedTasksWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $department = auth()->user()->primaryDepartment()?->name ?? 'cutting';
        
        $blockedTasks = WorkOrderTask::byDepartment($department)
            ->blocked()
            ->count();
            
        $materialShortages = MaterialLine::withShortages()
            ->whereHas('workOrder', function ($q) use ($department) {
                $q->where('current_department', $department);
            })
            ->count();

        return [
            Stat::make('Blocked Tasks', $blockedTasks)
                ->description('Production stopped')
                ->descriptionIcon('heroicon-o-stop')
                ->color('danger'),
                
            Stat::make('Material Shortages', $materialShortages)
                ->description('Waiting for materials')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}