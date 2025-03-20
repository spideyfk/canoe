<?php

namespace Database\Seeders;

use App\Models\Alias;
use App\Models\Fund;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Alias::factory()->count(50)->create();
    }
}
