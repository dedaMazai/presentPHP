<?php

namespace App\Models\V2\Sales\Customer;

use App\Models\Document\DocumentSubtype;
use App\Models\Role;
use App\Models\Sales\Gender;
use App\Models\Sales\OwnerType;
use App\Models\Sales\SignStatus;
use Carbon\Carbon;

/**
 * Class LegalEntityData
 *
 * @package App\Models\V2\Sales\Customer
 */
class LegalEntityData
{
    public function __construct(
        private readonly ?string $accountId,
        private readonly ?string $jointOwnerId,
        private readonly ?string $accountType,
        private readonly ?string $name,
        private readonly ?string $inn
    ) {
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function getJointOwnerId(): ?string
    {
        return $this->jointOwnerId;
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getInn(): ?string
    {
        return $this->inn;
    }
}
