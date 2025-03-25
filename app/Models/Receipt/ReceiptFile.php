<?php

namespace App\Models\Receipt;

/**
 * Class ReceiptFile
 *
 * @package App\Models\Receipt
 */
class ReceiptFile
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
