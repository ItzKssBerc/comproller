<?php

namespace App\Filament\Hr\Resources\Employees\Pages;

use App\Filament\Hr\Resources\Employees\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
}
