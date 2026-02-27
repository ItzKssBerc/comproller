<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryDetail extends Model
{
    protected $fillable = [
        'employee_id',
        'base_hourly_rate',
    ];

    protected function casts(): array
    {
        return [
            'base_hourly_rate' => 'encrypted',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
