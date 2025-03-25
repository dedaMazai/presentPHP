<?php

namespace App\Components\Views\Traits;

use App\Components\Views\Viewable as ViewableContract;

/**
 * Class CanView
 *
 * @package App\Components\Views\Traits
 */
trait CanView
{
    public function view(ViewableContract $object): void
    {
        $object->markAsViewed($this);
    }

    public function viewed(ViewableContract $object): bool
    {
        return $object->isViewed($this);
    }
}
