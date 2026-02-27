<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePersonalData extends Model
{
    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'mothers_name',
    ];

    protected function casts(): array
    {
        return [
            'first_name' => 'encrypted',
            'last_name' => 'encrypted',
            'date_of_birth' => 'encrypted',
            'mothers_name' => 'encrypted',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
