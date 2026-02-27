<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePenalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'description',
        'amount',
        'is_processed',
        'payroll_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'encrypted',
            'is_processed' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }
}
