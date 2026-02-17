<?php

namespace Zervi\Manufacturing\Filament\Resources\WorkOrderResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Zervi\Manufacturing\Filament\Resources\WorkOrderResource;

class EditWorkOrder extends EditRecord
{
    protected static string $resource = WorkOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
            \Filament\Actions\Action::make('view_kanban')
                ->label('View in Kanban')
                ->icon('heroicon-o-view-columns')
                ->url(route('filament.admin.pages.work-order-kanban')),
        ];
    }
}