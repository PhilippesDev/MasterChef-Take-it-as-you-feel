<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\OptionValue;

class PriceCalculator
{
    /**
     * Somme des supplements des valeurs choisies, en ne gardant que celles
     * qui appartiennent bien au plat demande.
     *
     * @param  list<int>  $valueIds
     */
    public function extras(Dish $dish, array $valueIds): float
    {
        $extras = OptionValue::query()
            ->whereIn('id', $valueIds)
            ->whereHas('group', fn ($query) => $query->where('dish_id', $dish->id))
            ->sum('extra_price');

        return round((float) $extras, 2);
    }

    /**
     * Prix d'une unite du plat avec ses reglages.
     *
     * @param  list<int>  $valueIds
     */
    public function unitPrice(Dish $dish, array $valueIds = []): float
    {
        return round((float) $dish->base_price + $this->extras($dish, $valueIds), 2);
    }

    /**
     * Prix total recalcule cote serveur, jamais celui envoye par le client.
     *
     * @param  list<int>  $valueIds
     */
    public function total(Dish $dish, array $valueIds = [], int $quantity = 1): float
    {
        return round($this->unitPrice($dish, $valueIds) * max($quantity, 0), 2);
    }
}
