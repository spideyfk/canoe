<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Company;
use App\Models\Fund;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FundManagerSeeder::class,
            CompanySeeder::class,
            FundSeeder::class
        ]);

        $this->seedPivotTable();
    }

    protected function seedPivotTable()
    {
        $funds = Fund::all();
        $companies = Company::all();

        $funds->each(function ($fund) use ($companies) {
            $fund->companies()->attach(
                $companies->random(rand(1, 3))->pluck('id')
            );
        });
    }
}
