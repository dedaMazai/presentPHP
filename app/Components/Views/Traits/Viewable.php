<?php

namespace App\Components\Views\Traits;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Components\Views\Viewable as ViewableModelInterface;

/**
 * Trait Viewable
 *
 * @property-read int $views_count
 *
 * @package App\Components\Views\Traits
 */
trait Viewable
{
    public static function bootViewable()
    {
        static::deleted(function (ViewableModelInterface $model) {
            $model->viewedUsers()->detach();
        });
    }

    public function viewedUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'viewable', 'views');
    }

    public function markAsViewed(User $user): void
    {
        $this->viewedUsers()->attach($user);
    }

    public function isViewed(User $user): bool
    {
        return $this->viewedUsers()->where('id', $user->id)->exists();
    }

    public function getViewsCountAttribute(): int
    {
        return $this->viewedUsers()->count();
    }

    public function scopeNotViewedByUser(Builder $query, User $user): Builder
    {
        return $query->doesntHave('viewedUsers', 'and', function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }

    public function scopeViewedByUser(Builder $query, User $user): Builder
    {
        return $query->whereHas('viewedUsers', function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }
}
