<?php

namespace App\Services\Sales;

use App\Models\Sales\Owner;

/**
 * Class OwnerRepository
 *
 * @package App\Services\Sales
 */
class OwnerRepository
{
    public function makeOwner(array $data): Owner
    {
        return new Owner(
            id: $data['id'],
            lastName: $data['lastName'],
            firstName: $data['firstName'],
            middleName: $data['middleName'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            passwordHash: $data['passwordHash'] ?? null,
            dhEmail: $data['dhEmail'] ?? null,
        );
    }
}
