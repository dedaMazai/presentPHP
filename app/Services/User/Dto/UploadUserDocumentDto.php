<?php

namespace App\Services\User\Dto;

use App\Models\Document\DocumentType;

/**
 * Class UploadUserDocumentDto
 *
 * @package App\Services\User\Dto
 */
class UploadUserDocumentDto
{
    public function __construct(
        public DocumentType $type,
        public string $body,
        public string $mimeType,
        public string $fileName
    ) {
    }
}
