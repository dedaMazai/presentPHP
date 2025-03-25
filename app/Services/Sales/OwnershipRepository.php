<?php

namespace App\Services\Sales;

use App\Models\Sales\Ownership;

/**
 * Class OwnershipRepository
 *
 * @package App\Services\Sales
 */
class OwnershipRepository
{
    public function makeOwnership(array $data): Ownership
    {
        return new Ownership(
            code: $data['code'],
            name: $data['name'],
            message: $data['message'] ?? '',
        );
    }
}
