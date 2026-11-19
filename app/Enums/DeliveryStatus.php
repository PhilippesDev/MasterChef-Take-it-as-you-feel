<?php

namespace App\Enums;

enum DeliveryStatus: string
{
    case Pending = 'pending';
    case PickedUp = 'picked_up';
    case Delivered = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::PickedUp => 'Récupérée',
            self::Delivered => 'Livrée',
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
