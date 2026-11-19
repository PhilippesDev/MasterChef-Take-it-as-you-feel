<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Delivering = 'delivering';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Preparing => 'En préparation',
            self::Ready => 'Prête',
            self::Delivering => 'En livraison',
            self::Delivered => 'Livrée',
            self::Cancelled => 'Annulée',
        };
    }

    /**
     * Transitions autorisees par le cycle de vie du README.
     *
     * @return list<self>
     */
    public function nextStatuses(): array
    {
        return match ($this) {
            self::Pending => [self::Preparing, self::Cancelled],
            self::Preparing => [self::Ready],
            self::Ready => [self::Delivering],
            self::Delivering => [self::Delivered],
            self::Delivered, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->nextStatuses(), true);
    }

    public function isFinal(): bool
    {
        return $this->nextStatuses() === [];
    }

    /**
     * Une commande ne peut etre annulee que tant qu'elle attend en cuisine.
     */
    public function canBeCancelled(): bool
    {
        return $this === self::Pending;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
