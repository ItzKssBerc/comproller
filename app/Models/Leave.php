<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'status',
        'reason',
        'payroll_id',
        'is_processed',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_processed' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Leave $leave) {
            // Sick leave must always be verified and approved manually
            if ($leave->type === 'sick_leave') {
                $leave->status = 'pending';
            } else {
                // Other leave types (paid, unpaid, other) are automatically approved upon creation
                $leave->status = 'approved';
            }
        });
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }
}
