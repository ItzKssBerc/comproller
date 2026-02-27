<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeFinancialData extends Model
{
    protected $fillable = [
        'employee_id',
        'tax_number',
        'social_security_number',
        'bank_account_number',
    ];

    protected function casts(): array
    {
        return [
            'tax_number' => 'encrypted',
            'social_security_number' => 'encrypted',
            'bank_account_number' => 'encrypted',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
