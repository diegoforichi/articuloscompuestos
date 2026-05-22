<?php

namespace Database\Factories;

use App\Models\ComponentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComponentType>
 */
class ComponentTypeFactory extends Factory
{
    protected $model = ComponentType::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = 'tipo-'.fake()->unique()->numerify('####');

        return [
            'code' => $code,
            'name' => fake()->words(2, true),
            'sort_order' => fake()->numberBetween(1, 500),
            'is_active' => true,
        ];
    }
}
