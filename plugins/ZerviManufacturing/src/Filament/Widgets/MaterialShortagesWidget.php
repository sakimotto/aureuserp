<?php

namespace Zervi\Manufacturing\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Zervi\Manufacturing\Models\QualityRecord;

class MaterialShortagesWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $department = auth()->user()->primaryDepartment()?->name ?? 'cutting';
        
        $qcFailures = QualityRecord::requiresResolution()
            ->byDepartment($department)
            ->count();
            
        $overdueResolutions = QualityRecord::overdueResolution()
            ->byDepartment($department)
            ->count();

        return [
            Stat::make('QC Failures', $qcFailures)
                ->description('Need resolution')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
                
            Stat::make('Overdue Resolutions', $overdueResolutions)
                ->description('Past due date')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}