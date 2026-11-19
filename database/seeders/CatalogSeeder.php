<?php

namespace Database\Seeders;

use App\Enums\OptionGroupType;
use App\Models\Dish;
use App\Models\OptionGroup;
use App\Models\OptionValue;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Catalogue de demonstration : trois plats et leurs reglages.
     */
    public function run(): void
    {
        foreach ($this->dishes() as $dishData) {
            $dish = Dish::updateOrCreate(
                ['name' => $dishData['name']],
                [
                    'description' => $dishData['description'],
                    'base_price' => $dishData['base_price'],
                    'image' => $dishData['image'],
                    'is_active' => true,
                ]
            );

            foreach ($dishData['groups'] as $groupData) {
                $group = OptionGroup::updateOrCreate(
                    ['dish_id' => $dish->id, 'name' => $groupData['name']],
                    [
                        'type' => $groupData['type'],
                        'is_required' => $groupData['is_required'],
                    ]
                );

                foreach ($groupData['values'] as $valueData) {
                    OptionValue::updateOrCreate(
                        ['option_group_id' => $group->id, 'label' => $valueData['label']],
                        ['extra_price' => $valueData['extra_price']]
                    );
                }
            }
        }
    }

    /**
     * @return list<array{name: string, description: string, base_price: float, image: ?string, groups: list<array{name: string, type: OptionGroupType, is_required: bool, values: list<array{label: string, extra_price: float}>}>}>
     */
    private function dishes(): array
    {
        return [
            [
                'name' => 'Burger Masterchef',
                'description' => 'Le burger signature, grille et assaisonne exactement comme vous le demandez, avec du citron si le cœur vous en dit.',
                'base_price' => 8.50,
                'image' => null,
                'groups' => [
                    [
                        'name' => 'Cuisson',
                        'type' => OptionGroupType::Choice,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Saignant', 'extra_price' => 0],
                            ['label' => 'À point', 'extra_price' => 0],
                            ['label' => 'Bien cuit', 'extra_price' => 0],
                        ],
                    ],
                    [
                        'name' => 'Sel',
                        'type' => OptionGroupType::Slider,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Pas de sel', 'extra_price' => 0],
                            ['label' => 'Léger', 'extra_price' => 0],
                            ['label' => 'Normal', 'extra_price' => 0],
                            ['label' => 'Bien salé', 'extra_price' => 0],
                        ],
                    ],
                    [
                        'name' => 'Piquant',
                        'type' => OptionGroupType::Slider,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Doux', 'extra_price' => 0],
                            ['label' => 'Moyen', 'extra_price' => 0],
                            ['label' => 'Fort', 'extra_price' => 0.50],
                        ],
                    ],
                    [
                        'name' => 'Citron',
                        'type' => OptionGroupType::Toggle,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Sans citron', 'extra_price' => 0],
                            ['label' => 'Avec citron', 'extra_price' => 0.30],
                        ],
                    ],
                    [
                        'name' => 'Vinaigre',
                        'type' => OptionGroupType::Toggle,
                        'is_required' => false,
                        'values' => [
                            ['label' => 'Sans vinaigre', 'extra_price' => 0],
                            ['label' => 'Avec vinaigre', 'extra_price' => 0],
                        ],
                    ],
                    [
                        'name' => 'Épices',
                        'type' => OptionGroupType::Choice,
                        'is_required' => false,
                        'values' => [
                            ['label' => 'Sans épices', 'extra_price' => 0],
                            ['label' => 'Épices douces', 'extra_price' => 0],
                            ['label' => 'Épices fortes', 'extra_price' => 0.50],
                        ],
                    ],
                    [
                        'name' => 'Extras',
                        'type' => OptionGroupType::Choice,
                        'is_required' => false,
                        'values' => [
                            ['label' => 'Fromage', 'extra_price' => 1.00],
                            ['label' => 'Bacon', 'extra_price' => 1.50],
                            ['label' => 'Œuf', 'extra_price' => 0.75],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Poulet braisé',
                'description' => 'Poulet mariné et braisé, accompagné selon votre goût, avec un piquant réglable.',
                'base_price' => 7.00,
                'image' => null,
                'groups' => [
                    [
                        'name' => 'Cuisson',
                        'type' => OptionGroupType::Choice,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Bien cuit', 'extra_price' => 0],
                            ['label' => 'Très cuit', 'extra_price' => 0],
                        ],
                    ],
                    [
                        'name' => 'Sel',
                        'type' => OptionGroupType::Slider,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Pas de sel', 'extra_price' => 0],
                            ['label' => 'Léger', 'extra_price' => 0],
                            ['label' => 'Normal', 'extra_price' => 0],
                            ['label' => 'Bien salé', 'extra_price' => 0],
                        ],
                    ],
                    [
                        'name' => 'Piquant',
                        'type' => OptionGroupType::Slider,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Doux', 'extra_price' => 0],
                            ['label' => 'Moyen', 'extra_price' => 0],
                            ['label' => 'Fort', 'extra_price' => 0.50],
                        ],
                    ],
                    [
                        'name' => 'Accompagnement',
                        'type' => OptionGroupType::Choice,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Riz', 'extra_price' => 0],
                            ['label' => 'Frites', 'extra_price' => 1.00],
                            ['label' => 'Plantain', 'extra_price' => 0.75],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Wrap végétarien',
                'description' => 'Un wrap frais et léger, à personnaliser avec la sauce et les extras de votre choix.',
                'base_price' => 6.00,
                'image' => null,
                'groups' => [
                    [
                        'name' => 'Sauce',
                        'type' => OptionGroupType::Choice,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Yaourt', 'extra_price' => 0],
                            ['label' => 'Piquante', 'extra_price' => 0.25],
                            ['label' => 'Sans sauce', 'extra_price' => 0],
                        ],
                    ],
                    [
                        'name' => 'Sel',
                        'type' => OptionGroupType::Slider,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Pas de sel', 'extra_price' => 0],
                            ['label' => 'Léger', 'extra_price' => 0],
                            ['label' => 'Normal', 'extra_price' => 0],
                        ],
                    ],
                    [
                        'name' => 'Piquant',
                        'type' => OptionGroupType::Slider,
                        'is_required' => true,
                        'values' => [
                            ['label' => 'Doux', 'extra_price' => 0],
                            ['label' => 'Moyen', 'extra_price' => 0],
                            ['label' => 'Fort', 'extra_price' => 0.50],
                        ],
                    ],
                    [
                        'name' => 'Extras',
                        'type' => OptionGroupType::Choice,
                        'is_required' => false,
                        'values' => [
                            ['label' => 'Avocat', 'extra_price' => 1.00],
                            ['label' => 'Fromage', 'extra_price' => 1.00],
                            ['label' => 'Tofu', 'extra_price' => 1.25],
                        ],
                    ],
                ],
            ],
        ];
    }
}
