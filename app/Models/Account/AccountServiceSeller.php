<?php

namespace App\Models\Account;

/**
 * Class AccountServiceSeller
 *
 * @package App\Models\Account
 */
class AccountServiceSeller
{
    public function __construct(
        private string $id,
        private string $name
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
