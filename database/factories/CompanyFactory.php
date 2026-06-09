<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
{
    return [
        'name'     => fake()->company() . ' B.V.',
        'industry' => fake()->randomElement(['Technologie', 'Zorg', 'Logistiek', 'Retail', 'Financiën', 'Productie']),
        'website'  => fake()->domainName(),
    ];
}
}
