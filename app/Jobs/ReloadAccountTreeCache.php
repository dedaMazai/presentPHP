<?php

namespace App\Jobs;

use App\Services\Claim\ClaimCatalogueService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReloadAccountTreeCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private $accountNumber,
    ) {
        logger()->debug('ReloadAccountTreeCache->construct: completed');
    }

    public $tries = 5;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ClaimCatalogueService $catalogueService
    ) {
        $catalogueService->reloadAccountTreeCache($this->accountNumber);
    }

    public function retryUntil()
    {
        return now()->addMinutes(10);
    }
}
