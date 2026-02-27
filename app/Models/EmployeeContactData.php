<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeContactData extends Model
{
    protected $fillable = [
        'employee_id',
        'phone',
        'email',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'phone' => 'encrypted',
            'email' => 'encrypted',
            'address' => 'encrypted',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
