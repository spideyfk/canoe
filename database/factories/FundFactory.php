<?php

namespace Database\Factories;

use App\Models\Alias;
use App\Models\Company;
use App\Models\Fund;
use App\Models\FundManager;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fund>
 */
class FundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'start_year' => $this->faker->numberBetween(2000, date('Y')),
            'manager_id' => FundManager::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Fund $fund) {
            Alias::factory()->count(rand(1, 5))->create(['fund_id' => $fund->id]);
            $companies = Company::inRandomOrder()->limit(rand(1,3))->get();
            $fund->companies()->attach($companies);
        });
    }
}
