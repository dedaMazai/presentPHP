<?php

namespace App\Models\V2\Sales;

/**
 * Class IconNavbar
 *
 * @package App\Models\Sales
 */
class IconNavbar
{
    public function __construct(
        private int $number,
        private ?string $activeIcon,
        private ?string $disableIcon,
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
    public function getActiveIcon(): ?string
    {
        return $this->activeIcon;
    }

    /**
     * @return string|null
     */
    public function getDisableIcon(): ?string
    {
        return $this->disableIcon;
    }
}
