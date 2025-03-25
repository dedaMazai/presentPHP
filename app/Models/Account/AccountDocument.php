<?php

namespace App\Models\Account;

/**
 * Class AccountDocument
 *
 * @package App\Models\Account
 */
class AccountDocument
{
    public function __construct(
        private string $id,
        private string $name,
        private string $url,
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

    public function getUrl(): string
    {
        return $this->url;
    }
}
