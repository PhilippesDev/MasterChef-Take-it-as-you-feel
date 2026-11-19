<?php

namespace App\Enums;

enum OptionGroupType: string
{
    case Choice = 'choice';
    case Slider = 'slider';
    case Toggle = 'toggle';

    public function label(): string
    {
        return match ($this) {
            self::Choice => 'Choix',
            self::Slider => 'Curseur',
            self::Toggle => 'Interrupteur',
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
