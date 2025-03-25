<?php

namespace App\Components\Publication;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Publicable
 *
 * Entity could be published
 *
 * @package App\Components\Publication
 */
trait Publicable
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function unpublish(): void
    {
        $this->updatePublicationStatus(false);
    }

    public function updatePublicationStatus(bool $status): void
    {
        $this->is_published = $status;
        $this->saveOrFail();
    }
}
