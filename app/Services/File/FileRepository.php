<?php

namespace App\Services\File;

use App\Models\Document\Document;
use App\Models\Document\DocumentProcessingStatus;
use App\Models\Document\DocumentType;
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Exception;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use function collect;
use function now;

/**
 * Class DocumentRepository
 *
 * @package App\Services\Document
 */
class FileRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private CacheInterface $cache,
    ) {
    }

    public function getFileById(string $id): ?Document
    {
        try {
            $data = $this->dynamicsCrmClient->getDocumentById($id);

            return $this->makeDocument($data);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param User $user
     *
     * @return Document[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getFileByUser(User $user): array
    {
        return collect($this->dynamicsCrmClient->getCustomerDocuments($user->crm_id)['documentList'] ?? [])
            ->filter(fn(array $item) => in_array($item['documentType']['code'] ?? '', DocumentType::toValues()))
            ->sortByDesc('createdOn')
            ->unique(fn(array $item) => $item['documentType']['code'])
            ->filter(fn(array $item) => !in_array($item['id'], $this->getDeletedDocumentIdsByUser($user)))
            ->map(fn(array $item) => $this->makeDocument($item))
            ->toArray();
    }

    /**
     * @param User $user
     *
     * @return string[]
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getFileIdsByUser(User $user): array
    {
        $documentIds = $this->cache->get($this->getDocumentCacheKeyByUser($user));

        if ($documentIds !== null) {
            return array_filter(
                $documentIds,
                fn(string $id) => !in_array($id, $this->getDeletedFileIdsByUser($user))
            );
        }

        return array_map(fn(Document $document) => $document->getId(), $this->getDocumentsByUser($user));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setFilesCacheIdsByUser(User $user, array $documents): void
    {
        $this->cache->set(
            $this->getFileCacheKeyByUser($user),
            array_map(fn(Document $document) => $document->getId(), $documents),
            now()->addDay()
        );
    }

    private function getFileCacheKeyByUser(User $user): string
    {
        return "user.{$user->id}.documents";
    }

    private function getDeletedFileIdsByUser(User $user): array
    {
        return $user->deletedDocuments()->pluck('document_id')->toArray();
    }

    private function makeDocument(array $data): Document
    {
        return new Document(
            id: $data['id'],
            name: $data['name'],
            fileName: $data['fileName'],
            mimeType: $data['mimeType'],
            type: DocumentType::from($data['documentType']['code']),
            processingStatus: DocumentProcessingStatus::from($data['documentProcessingStatus']['code']),
            body: $data['documentBody'] ?? null
        );
    }
}
