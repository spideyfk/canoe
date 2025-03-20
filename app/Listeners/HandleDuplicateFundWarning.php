<?php

namespace App\Listeners;

use App\Events\DuplicateFundWarning;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleDuplicateFundWarning implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(DuplicateFundWarning $event): void
    {
        \Log::warning('Potential duplicate funds detected:', [
            'duplicates' => $event->duplicates,
        ]);
    }
}
