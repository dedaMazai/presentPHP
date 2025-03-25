<?php

namespace App\Models\Sales;

/**
 * Class Document
 *
 * @package App\Models\Document
 */
class SalesDocument
{
    public function __construct(
        private string $id,
        private string $name,
        private string $document,
        private string $mimeType,
        private string $type,
        private string $processingStatus,
        private string $status,
        private string $url,
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDocument(): string
    {
        return $this->document;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getProcessingStatus(): string
    {
        return $this->processingStatus;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
