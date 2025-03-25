<?php

namespace App\Models\Claim;

/**
 * Class ClaimImageContent
 *
 * @package App\Models\Claim
 */
class ClaimImageContent
{
    public function __construct(
        private string $name,
        private string $fileName,
        private string $documentBody,
        private string $mimeType,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getDocumentBody(): string
    {
        return $this->documentBody;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
