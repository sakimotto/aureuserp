<?php

namespace Zervi\Manufacturing\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Zervi\Manufacturing\Models\WorkOrder;

class OverdueJobsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $departmentId = auth()->user()->primaryDepartment()?->id;
        
        if (!$departmentId) {
            return [];
        }

        $overdueJobs = WorkOrder::forSupervisorDashboard($departmentId)
            ->overdue()
            ->count();
            
        $overdueValue = WorkOrder::forSupervisorDashboard($departmentId)
            ->overdue()
            ->sum('estimated_material_cost');

        return [
            Stat::make('Overdue Jobs', $overdueJobs)
                ->description('Past promised delivery')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
                
            Stat::make('Overdue Value', '฿' . number_format($overdueValue, 0))
                ->description('Financial impact')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('danger'),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}