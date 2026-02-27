<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeIdentification extends Model
{
    protected $fillable = [
        'employee_id',
        'citizenship',
        'id_card_number',
        'identification_device',
        'device_identifier',
        'qr_code_hash',
    ];

    protected function casts(): array
    {
        return [
            'citizenship' => 'encrypted',
            'id_card_number' => 'encrypted',
            'device_identifier' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $identification) {
            if ($identification->identification_device === 'qr_code' || $identification->qr_code_hash === null) {
                $identification->qr_code_hash = $identification->employee->generateQrCodeHash($identification);

                if ($identification->identification_device === 'qr_code') {
                    $identification->device_identifier = $identification->qr_code_hash;
                }
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
