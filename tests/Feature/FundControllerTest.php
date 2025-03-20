<?php

namespace Feature;

use App\Models\Company;
use App\Models\Fund;
use App\Models\FundManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FundControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_funds_with_filters()
    {
        $manager = FundManager::factory()->create(['name' => 'Michael Scott']);
        Fund::factory()->create([
            'name' => 'ABC Hedge Fund',
            'start_year' => 2025,
            'manager_id' => $manager->id,
        ]);

        $response = $this->getJson('/api/funds?name=ABC Hedge Fund&manager=Michael Scott&year=2025');
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'ABC Hedge Fund']);
    }

    /** @test */
    public function it_creates_a_fund()
    {
        $manager = FundManager::factory()->create();
        $company = Company::factory()->create();

        $response = $this->postJson('/api/funds', [
            'name' => 'ABC Hedge Fund',
            'start_year' => 2025,
            'manager_id' => $manager->id,
            'aliases' => [
                ['name' => 'Custom Alias'],
            ],
            'company_ids' => [$company->id],
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'ABC Hedge Fund']);
    }

    /** @test */
    public function it_updates_a_fund()
    {
        $fund = Fund::factory()->create();

        $response = $this->putJson("/api/funds/{$fund->id}", [
            'name' => 'Updated Fund',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Fund']);
    }

    /** @test */
    public function it_gets_potential_duplicates()
    {
        $manager = FundManager::factory()->create();
        $fund = Fund::factory()->create(['manager_id' => $manager->id]);

        $response = $this->postJson('/api/funds/potential-duplicates', [
            'name' => $fund->name,
            'manager_id' => $manager->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }
}
