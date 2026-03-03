<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'locker_key',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'locker_key' => 'encrypted:array',
        ];
    }

    public function personalData()
    {
        return $this->hasOne(EmployeePersonalData::class);
    }

    protected function fullName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                $this->loadMissing('personalData');
                if (! $this->personalData) {
                    return __('Unknown').' #'.$this->id;
                }

                return trim($this->personalData->last_name.' '.$this->personalData->first_name);
            },
        );
    }

    public function contactData()
    {
        return $this->hasOne(EmployeeContactData::class);
    }

    public function financialData(): HasOne
    {
        return $this->hasOne(EmployeeFinancialData::class);
    }

    public function identification(): HasOne
    {
        return $this->hasOne(EmployeeIdentification::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(EmployeeContract::class);
    }

    public function salaryDetail(): HasOne
    {
        return $this->hasOne(EmployeeSalaryDetail::class);
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function leaves(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function generateQrCodeHash(?EmployeeIdentification $identification = null): string
    {
        $this->load(['personalData', 'financialData']);

        if (! $identification) {
            $this->load('identification');
            $identification = $this->identification;
        }

        $personal = $this->personalData;
        $financial = $this->financialData;

        $lastName = $personal?->last_name ?? '';
        $firstName = $personal?->first_name ?? '';
        $taxNumber = $financial?->tax_number ?? '';
        $ssn = $financial?->social_security_number ?? '';
        $idCard = $identification?->id_card_number ?? '';
        $citizenship = $identification?->citizenship ?? '';

        return hash('sha256', sprintf(
            '%s-%s-%s-%s-%s-%s-%s-%s-%s-%s-%s-%s',
            $lastName,
            $firstName,
            $firstName,
            $lastName,
            $taxNumber,
            $ssn,
            $ssn,
            $taxNumber,
            $idCard,
            $citizenship,
            $citizenship,
            $idCard
        ));
    }

    public function penalties(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmployeePenalty::class);
    }
}
