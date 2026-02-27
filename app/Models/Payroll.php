<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'period',
        'base_salary',
        'attendance_hours',
        'paid_leave_hours',
        'sick_leave_hours',
        'total_hours',
        'gross_amount',
        'total_deductions',
        'net_amount',
        'status',
        'is_forwarded',
    ];

    protected function casts(): array
    {
        return [
            'is_forwarded' => 'boolean',
            'base_salary' => 'encrypted',
            'attendance_hours' => 'encrypted',
            'paid_leave_hours' => 'encrypted',
            'sick_leave_hours' => 'encrypted',
            'total_hours' => 'encrypted',
            'gross_amount' => 'encrypted',
            'total_deductions' => 'encrypted',
            'net_amount' => 'encrypted',
        ];
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function penalties(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmployeePenalty::class);
    }
}
