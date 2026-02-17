<?php

namespace Zervi\Manufacturing\Filament\Pages;

use Filament\Pages\Dashboard;
use Filament\Pages\Page;

class SupervisorDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Supervisor Dashboard';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            \Zervi\Manufacturing\Filament\Widgets\ActiveJobsWidget::class,
            \Zervi\Manufacturing\Filament\Widgets\OverdueJobsWidget::class,
            \Zervi\Manufacturing\Filament\Widgets\BlockedTasksWidget::class,
            \Zervi\Manufacturing\Filament\Widgets\MaterialShortagesWidget::class,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('kanban')
                ->label('Production Board')
                ->icon('heroicon-o-view-columns')
                ->url(route('filament.admin.pages.work-order-kanban')),
                
            \Filament\Actions\Action::make('new_work_order')
                ->label('New Work Order')
                ->icon('heroicon-o-plus')
                ->url(route('filament.admin.resources.zervi-work-orders.create')),
        ];
    }

    public function getColumns(): int | array
    {
        return 4;
    }
}