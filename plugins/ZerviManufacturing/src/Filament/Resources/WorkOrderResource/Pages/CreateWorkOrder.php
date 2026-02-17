<?php

namespace Zervi\Manufacturing\Filament\Resources\WorkOrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Zervi\Manufacturing\Filament\Resources\WorkOrderResource;

class CreateWorkOrder extends CreateRecord
{
    protected static string $resource = WorkOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['owning_department_id'] = auth()->user()->primaryDepartment()?->id;
        
        return $data;
    }
}