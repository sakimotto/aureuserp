<?php

namespace Zervi\Manufacturing\Filament\Resources\WorkOrderResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Zervi\Manufacturing\Filament\Resources\WorkOrderResource;

class ListWorkOrders extends ListRecords
{
    protected static string $resource = WorkOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
            \Filament\Actions\Action::make('kanban')
                ->label('Production Board')
                ->icon('heroicon-o-view-columns')
                ->color('primary')
                ->url(route('filament.admin.pages.work-order-kanban')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \Zervi\Manufacturing\Filament\Widgets\ActiveJobsWidget::class,
            \Zervi\Manufacturing\Filament\Widgets\OverdueJobsWidget::class,
            \Zervi\Manufacturing\Filament\Widgets\BlockedTasksWidget::class,
            \Zervi\Manufacturing\Filament\Widgets\MaterialShortagesWidget::class,
        ];
    }
}