<?php

namespace App\Models\Claim;

use App\Models\Document\DocumentType;

/**
 * Class ClaimImage
 *
 * @package App\Models\Claim
 */
class ClaimImage
{
    public function __construct(
        private string $id,
        private string $name,
        private DocumentType $type,
        private string $url,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): DocumentType
    {
        return $this->type;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
