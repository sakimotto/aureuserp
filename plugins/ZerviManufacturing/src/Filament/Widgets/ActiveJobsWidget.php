<?php

namespace Zervi\Manufacturing\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Zervi\Manufacturing\Models\WorkOrder;

class ActiveJobsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $departmentId = auth()->user()->primaryDepartment()?->id;
        
        if (!$departmentId) {
            return [];
        }

        $activeJobs = WorkOrder::forSupervisorDashboard($departmentId)->count();
        $inProgressJobs = WorkOrder::forSupervisorDashboard($departmentId)
            ->where('status', 'in_progress')
            ->count();

        return [
            Stat::make('Active Jobs', $activeJobs)
                ->description('Currently in production')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('primary'),
                
            Stat::make('In Progress', $inProgressJobs)
                ->description('Actively being worked')
                ->descriptionIcon('heroicon-o-play')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}