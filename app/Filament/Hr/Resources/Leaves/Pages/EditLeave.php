<?php

namespace App\Filament\Hr\Resources\Leaves\Pages;

use App\Filament\Hr\Resources\Leaves\LeaveResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeave extends EditRecord
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
