<?php

namespace App\Services\Claim;

use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ClaimCatalogueService
 *
 * @package App\Services\Claim
 */
class ClaimCatalogueService
{
    public function __construct(
        private ClaimCatalogueRepository $catalogueRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function reloadTreeCache(): void
    {
        $this->catalogueRepository->reloadTreeCache();
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function reloadAccountTreeCache(string $accountNumber): void
    {
        $this->catalogueRepository->reloadAccountTreeCache($accountNumber);
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function reloadPopularServicesCache(): void
    {
        $this->catalogueRepository->reloadPopularServicesCache();
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function reloadAccountPopularServicesCache(string $accountNumber): void
    {
        $this->catalogueRepository->reloadAccountPopularServicesCache($accountNumber);
    }
}
