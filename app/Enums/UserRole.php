<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel
{
    case App = 'alkalmazás';
    case Admin = 'rendszergazda';
    case HR = 'hr';
    case Camera = 'kamera';
    case Finance = 'pénzügy';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::App => 'Alkalmazás (Super Admin)',
            self::Admin => 'Rendszergazda',
            self::HR => 'HR',
            self::Camera => 'Kamera',
            self::Finance => 'Pénzügy',
        };
    }
}
