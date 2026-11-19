<?php

namespace App\Enums;

enum UserRole: string
{
    case Client = 'client';
    case Chef = 'chef';
    case Livreur = 'livreur';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Client',
            self::Chef => 'Chef',
            self::Livreur => 'Livreur',
            self::Admin => 'Administrateur',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
