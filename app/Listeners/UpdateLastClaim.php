<?php

namespace App\Listeners;

use App\Events\NewLastClaim;
use App\Services\Claim\ClaimService;
use Psr\SimpleCache\InvalidArgumentException;

class UpdateLastClaim
{
    public function __construct(private ClaimService $claimService)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(NewLastClaim $event): void
    {
        $this->claimService->updateLastClaimId($event->getAccountNumber(), $event->getClaimRawData());
    }
}
