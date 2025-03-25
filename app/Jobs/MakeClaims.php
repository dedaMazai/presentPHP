<?php

namespace App\Jobs;

use App\Services\Claim\MakeClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MakeClaims implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private $shortClaims,
        private $accountNumber,
    ) {
        logger()->debug('MakeClaims->construct: completed');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        MakeClaim $makeClaim
    ) {
        $makeClaim->makeClaims($this->shortClaims, $this->accountNumber);
    }
}
