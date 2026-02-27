<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeContract extends Model
{
    protected $fillable = [
        'employee_id',
        'employment_type',
        'employment_term',
        'position',
        'shift',
        'scheduled_activation_at',
        'scheduled_deactivation_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_activation_at' => 'datetime',
            'scheduled_deactivation_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
