<?php

namespace App\Components\Views;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Interface Viewable
 *
 * @package App\Components\Views
 */
interface Viewable
{
    public function markAsViewed(User $user): void;

    public function isViewed(User $user): bool;

    public function viewedUsers(): MorphToMany;
}
