<?php

namespace Database\Factories;

use App\Enums\DealStage;
use App\Models\Company;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deal>
 */
class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id'          => Company::factory(),
            'contact_id'          => null,
            'title'               => fake()->randomElement(['Overname', 'Participatie', 'Buy-and-build', 'Carve-out']) . ' ' . fake()->company(),
            'value'               => fake()->numberBetween(250, 25000) * 1000,
            'stage'               => fake()->randomElement(DealStage::cases()),
            'expected_close_date' => fake()->dateTimeBetween('now', '+6 months'),
            'notes'               => fake()->optional()->sentence(),
        ];
    }
}