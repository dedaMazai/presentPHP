<?php

namespace App\Models\Sales;

/**
 * Class Ownership
 *
 * @package App\Models\Sales
 */
class Ownership
{
    public function __construct(
        private int $code,
        private string $name,
        private string $message,
    ) {
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
