<?php

namespace App\Models\Document;

/**
 * Class Document
 *
 * @package App\Models\Document
 */
class Document
{
    public function __construct(
        private ?string $id,
        private ?string $name,
        private ?string $fileName,
        private ?string $mimeType,
        private ?DocumentType $type,
        private ?DocumentProcessingStatus $processingStatus,
        private ?string $body = null,
        private ?string $reasonFailure = null,
        private ?bool $isDocumentApprove = null,
        private ?string $objectCode = null,
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getProcessingStatus(): ?DocumentProcessingStatus
    {
        return $this->processingStatus;
    }

    public function getType(): ?DocumentType
    {
        return $this->type;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getReasonFailure(): ?string
    {
        return $this->reasonFailure;
    }

    public function getIsDocumentApprove(): ?bool
    {
        return $this->isDocumentApprove;
    }

    public function getObjectCode(): ?string
    {
        return $this->objectCode;
    }

    public function setObjectCode(?string $objectCode): void
    {
        $this->objectCode = $objectCode;
    }
}
