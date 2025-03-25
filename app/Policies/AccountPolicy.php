<?php

namespace App\Policies;

use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AccountPolicy
 *
 * @package App\Policies
 */
class AccountPolicy
{
    use HandlesAuthorization;

    public function view(User $user, string $accountNumber): bool
    {
        return $user->hasAccountRight($accountNumber);
    }

    public function manipulateRelationship(User $user, string $accountNumber): bool
    {
        return $user->hasOwnerAccountRight($accountNumber);
    }
}
