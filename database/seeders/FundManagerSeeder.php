<?php

namespace Database\Seeders;

use App\Models\FundManager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FundManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FundManager::factory()->count(20)->create();
    }
}
