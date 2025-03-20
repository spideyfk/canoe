<?php

namespace Tests\Unit;

use App\Models\Alias;
use App\Models\Company;
use App\Models\Fund;
use App\Models\FundManager;
use App\Repositories\FundRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FundRepositoryTest extends TestCase
{
    use RefreshDatabase;
    protected FundRepository $fundRepository;
    protected function setUp(): void
    {
        parent::setUp();
        $this->fundRepository = new FundRepository();
    }

    /** @test */
    public function it_creates_a_fund_with_aliases_and_companies()
    {
        $manager = FundManager::factory()->create();
        $company = Company::factory()->create();

        $data = [
            'name' => 'ABC Hedge Fund',
            'start_year' => 2025,
            'manager_id' => $manager->id,
            'aliases' => [
                ['name' => 'DEF Hedge'],
                ['name' => 'GHI Hedge'],
            ],
            'company_ids' => [$company->id],
        ];

        $fund = $this->fundRepository->create($data);

        $this->assertInstanceOf(Fund::class, $fund);
        $this->assertEquals('ABC Hedge Fund', $fund->name);
        $this->assertCount(2, $fund->aliases);
        $this->assertCount(1, $fund->companies);
    }

    /** @test */
    public function it_filters_funds_by_name_manager_and_year()
    {
        $manager = FundManager::factory()->create(['name' => 'Michael Scott']);
        Fund::factory()->create([
            'name' => 'ABC Hedge Fund',
            'start_year' => 2025,
            'manager_id' => $manager->id,
        ]);

        $funds = $this->fundRepository->getFilteredFunds('ABC Hedge Fund', 'Michael Scott', 2025);

        $this->assertCount(1, $funds);
        $this->assertEquals('ABC Hedge Fund', $funds->first()->name);
    }

    /** @test */
    public function it_updates_a_fund_with_aliases_and_companies()
    {
        $fund = Fund::factory()->create();
        $company = Company::factory()->create();

        $data = [
            'name' => 'ABC Hedge Updated',
            'aliases' => [
                ['name' => 'New Test Alias'],
            ],
            'company_ids' => [$company->id],
        ];

        $updatedFund = $this->fundRepository->updateFund($fund->id, $data);

        $this->assertEquals('ABC Hedge Updated', $updatedFund->name);
        $this->assertCount(1, $updatedFund->aliases);
        $this->assertCount(1, $updatedFund->companies);
    }

    /** @test */
    public function it_finds_potential_duplicates_by_name_or_alias()
    {
        $manager = FundManager::factory()->create();
        $fund = Fund::factory()->create(['manager_id' => $manager->id]);
        Alias::factory()->create(['fund_id' => $fund->id, 'name' => 'lalala hedge']);

        $duplicates = $this->fundRepository->findPotentialDuplicates('lalala hedge', $manager->id);

        $this->assertCount(1, $duplicates);
        $this->assertEquals($fund->id, $duplicates->first()->id);
    }

    /** @test */
    public function it_fails_to_create_a_fund_with_invalid_data()
    {
        $manager = FundManager::factory()->create();

        // Missing required fields
        $data = [
            'start_year' => 2025,
            'manager_id' => $manager->id,
        ];

        $this->expectException(\Exception::class);
        $this->fundRepository->create($data);

        // Invalid manager_id
        $data = [
            'name' => 'Invalid Fund',
            'start_year' => 2025,
            'manager_id' => 999, // Non-existent manager
        ];

        $this->expectException(\Exception::class);
        $this->fundRepository->create($data);

        // Invalid company_ids
        $data = [
            'name' => 'Invalid Fund',
            'start_year' => 2025,
            'manager_id' => $manager->id,
            'company_ids' => [999], // Non-existent company
        ];

        $this->expectException(\Exception::class);
        $this->fundRepository->create($data);
    }

    /** @test */
    public function it_fails_to_update_a_fund_with_invalid_data()
    {
        $fund = Fund::factory()->create();
        $manager = FundManager::factory()->create();

        // Invalid fund_id
        $data = [
            'name' => 'Updated Fund',
        ];

        $this->expectException(\Exception::class);
        $this->fundRepository->updateFund(999, $data); // Non-existent fund

        // Invalid manager_id
        $data = [
            'manager_id' => 999, // Non-existent manager
        ];

        $this->expectException(\Exception::class);
        $this->fundRepository->updateFund($fund->id, $data);

        // Invalid company_ids
        $data = [
            'company_ids' => [999], // Non-existent company
        ];

        $this->expectException(\Exception::class);
        $this->fundRepository->updateFund($fund->id, $data);
    }

    /** @test */
    public function it_returns_empty_when_filtering_with_no_matches()
    {
        $manager = FundManager::factory()->create(['name' => 'Michael Scott']);
        Fund::factory()->create([
            'name' => 'ABC Hedge Fund',
            'start_year' => 2025,
            'manager_id' => $manager->id,
        ]);

        // No matching name
        $funds = $this->fundRepository->getFilteredFunds('Non-existent Fund', 'Michael Scott', 2025);
        $this->assertCount(0, $funds);

        // No matching manager
        $funds = $this->fundRepository->getFilteredFunds('ABC Hedge Fund', 'Non-existent Manager', 2025);
        $this->assertCount(0, $funds);

        // No matching year
        $funds = $this->fundRepository->getFilteredFunds('ABC Hedge Fund', 'Michael Scott', 2024);
        $this->assertCount(0, $funds);
    }

    /** @test */
    public function it_returns_empty_when_finding_no_potential_duplicates()
    {
        $manager = FundManager::factory()->create();
        $fund = Fund::factory()->create(['manager_id' => $manager->id]);
        Alias::factory()->create(['fund_id' => $fund->id, 'name' => 'lalala hedge']);

        // No matching name or alias
        $duplicates = $this->fundRepository->findPotentialDuplicates('Non-existent Alias', $manager->id);
        $this->assertCount(0, $duplicates);

        // No matching manager
        $duplicates = $this->fundRepository->findPotentialDuplicates('lalala hedge', 999); // Non-existent manager
        $this->assertCount(0, $duplicates);
    }

    /** @test */
    public function it_fails_when_relationships_are_invalid()
    {
        $manager = FundManager::factory()->create();

        // Missing manager_id
        $data = [
            'name' => 'Fund Without Manager',
            'start_year' => 2025,
        ];

        $this->expectException(\Exception::class);
        $this->fundRepository->create($data);

        // Invalid company_ids
        $data = [
            'name' => 'Fund With Invalid Companies',
            'start_year' => 2025,
            'manager_id' => $manager->id,
            'company_ids' => [999], // Non-existent company
        ];

        $this->expectException(\Exception::class);
        $this->fundRepository->create($data);
    }

    /** @test */
    public function it_rolls_back_transactions_on_failure()
    {
        $manager = FundManager::factory()->create();

        // Simulate a failure by providing invalid data
        $data = [
            'name' => 'Fund With Invalid Data',
            'start_year' => 2025,
            'manager_id' => $manager->id,
            'company_ids' => [999], // This company does not exist
        ];

        try {
            $this->fundRepository->create($data);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }

        // Ensure no fund was created
        $this->assertDatabaseMissing('funds', ['name' => 'Fund With Invalid Data']);
    }
}
