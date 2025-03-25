<?php

namespace App\Services\V3\Sales;

use App\Models\User\User;
use App\Models\V3\Contract\Contract;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\V3\Contract\ContractRepository;

/**
 * Class ContractService
 *
 * @package App\Services\Sales
 */
class ContractService
{
    public function __construct(
        private readonly ContractRepository $contractRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function findContract(string $id, User $user): Contract
    {
        return $this->contractRepository->getById($id, $user);
    }
}
