<?php

namespace Tests\Unit;

use App\Events\DuplicateFundWarning;
use App\Models\Fund;
use App\Models\FundManager;
use App\Repositories\FundRepository;
use App\Services\FundService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FundServiceTest extends TestCase
{
    use RefreshDatabase;
    protected FundService $fundService;
    protected FundRepository $fundRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fundRepository = $this->createMock(FundRepository::class);
        $this->fundService = new FundService($this->fundRepository);
    }

    /** @test */
    public function it_creates_a_fund_and_dispatches_duplicate_warning_event()
    {
        Event::fake();

        $manager = FundManager::factory()->create();
        $data = [
            'name' => 'Test Fund',
            'start_year' => 2023,
            'manager_id' => $manager->id,
        ];

        $this->fundRepository->method('findPotentialDuplicates')
            ->willReturn(collect([Fund::factory()->create()]));

        $this->fundRepository->method('create')
            ->willReturn(Fund::factory()->create($data));

        $response = $this->fundService->createFund($data);

        Event::assertDispatched(DuplicateFundWarning::class);
        $this->assertArrayHasKey('warnings', $response);
    }

    /** @test */
    public function it_gets_potential_duplicates()
    {
        $manager = FundManager::factory()->create();
        $fund = Fund::factory()->create(['manager_id' => $manager->id]);

        $this->fundRepository->method('findPotentialDuplicates')
            ->willReturn(collect([$fund]));

        $duplicates = $this->fundService->getPotentialDuplicates('Test Fund', $manager->id);

        $this->assertCount(1, $duplicates);
    }
}
