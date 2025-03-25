<?php

namespace App\Models\Claim\ClaimCatalogue;

/**
 * Class ClaimCatalogueItemImages
 *
 * @package App\Models\Claim\ClaimCatalogue
 */
class ClaimCatalogueItemImages
{
    /**
     * @param string|null $mainLarge
     * @param string|null $mainMiddle
     * @param string|null $mainSmall
     * @param string|null $catalogue
     * @param string[]    $carousel
     */
    public function __construct(
        private ?string $mainLarge,
        private ?string $mainMiddle,
        private ?string $mainSmall,
        private ?string $catalogue,
        private array $carousel,
    ) {
    }

    public function getMainLarge(): ?string
    {
        return $this->mainLarge;
    }

    public function getMainMiddle(): ?string
    {
        return $this->mainMiddle;
    }

    public function getMainSmall(): ?string
    {
        return $this->mainSmall;
    }

    public function getCatalogue(): ?string
    {
        return $this->catalogue;
    }

    /**
     * @return string[]
     */
    public function getCarousel(): array
    {
        return $this->carousel;
    }
}
