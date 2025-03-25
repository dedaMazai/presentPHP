<?php

namespace App\Models\V2\Sales;

use App\Models\Sales\StageStatus;

/**
 * Class SaleSubstage
 *
 * @package App\Models\Sales
 */
class SaleSubstage
{
    public function __construct(
        private int $number,
        private ?string $name,
        private ?StageStatus $status,
        private ?string $icon,
        private ?string $code,
        private ?string $statusMessage,
        private ?bool $statusIcon,
        private ?IconNavbar $iconNavbar,
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

    /**
     * @return string|null
     */
    public function getStatusMessage(): ?string
    {
        return $this->statusMessage;
    }

    /**
     * @return bool|null
     */
    public function getStatusIcon(): ?bool
    {
        return $this->statusIcon;
    }

    /**
     * @return array|null
     */
    public function getIconNavbar(): ?IconNavbar
    {
        return $this->iconNavbar;
    }
}
