<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class NewLastClaim
 *
 * @package App\Events
 */
class NewLastClaim
{
    use Dispatchable, SerializesModels;

    public function __construct(
        private string $accountNumber,
        private ?array $claimRawData,
    ) {
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getClaimRawData(): ?array
    {
        return $this->claimRawData;
    }
}
