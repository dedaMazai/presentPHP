<?php

namespace App\Models\V2\Sales;

/**
 * Class SaleStage
 *
 * @package App\Models\Sales
 */
class SaleStage
{
    public function __construct(
        private int $number,
        private ?string $name,
        private ?string $status,
        private ?array $substages,
        private ?string $message,
        private ?string $icon,
        private ?string $code,
    ) {
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return array|null
     */
    public function getSubstages(): ?array
    {
        return $this->substages;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }
}
