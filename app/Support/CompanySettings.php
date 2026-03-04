<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;

class CompanySettings
{
    public static function name(string $fallback = 'Comproller'): string
    {
        try {
            return cache()->remember('company_name', now()->addHour(), fn() => User::where('role', UserRole::App->value)->value('name') ?? $fallback) ?? $fallback;
        }
        catch (\Throwable) {
            return $fallback;
        }
    }
}