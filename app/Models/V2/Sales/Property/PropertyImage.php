<?php

namespace App\Models\V2\Sales\Property;

/**
 * Class PropertyImage
 *
 * @package App\Models\Sales\Property
 */
class PropertyImage
{
    public function __construct(
        private ?string $name,
        private ?string $url,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}
