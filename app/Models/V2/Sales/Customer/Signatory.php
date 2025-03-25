<?php

namespace App\Models\V2\Sales\Customer;

use App\Models\Document\DocumentSubtype;
use App\Models\Role;
use App\Models\Sales\Gender;
use App\Models\Sales\OwnerType;
use App\Models\Sales\SignStatus;
use Carbon\Carbon;

/**
 * Class Signatory
 *
 * @package App\Models\V2\Sales\Customer
 */
class Signatory
{
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $fullName,
        private readonly ?string $label,
        private readonly ?string $signatory,
    ) {
    }


    public function getId(): string
    {
        return $this->id;
    }
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getSignatory(): ?string
    {
        return $this->signatory;
    }

    public function getLabel()
    {
        return $this->label;
    }
}
