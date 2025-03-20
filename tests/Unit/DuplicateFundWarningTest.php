<?php

namespace Tests\Unit;

use App\Events\DuplicateFundWarning;
use App\Listeners\HandleDuplicateFundWarning;
use App\Models\Fund;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DuplicateFundWarningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatches_duplicate_fund_warning_event()
    {
        Event::fake();

        $funds = collect([Fund::factory()->create()]);
        event(new DuplicateFundWarning($funds));

        Event::assertDispatched(DuplicateFundWarning::class);
    }
}
