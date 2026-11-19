<?php

namespace Database\Factories;

use App\Enums\OptionGroupType;
use App\Models\Dish;
use App\Models\OptionGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OptionGroup>
 */
class OptionGroupFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dish_id' => Dish::factory(),
            'name' => fake()->randomElement(['Sel', 'Piquant', 'Cuisson', 'Citron', 'Vinaigre', 'Épices']),
            'type' => fake()->randomElement(OptionGroupType::cases()),
            'is_required' => fake()->boolean(),
        ];
    }

    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }
}
