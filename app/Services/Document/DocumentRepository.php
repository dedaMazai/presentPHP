<?php

namespace App\Services\Document;

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
class DocumentRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private CacheInterface $cache,
    ) {
    }

    public function getDocumentById(string $id): ?Document
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
    public function getDocumentsByUser(User $user): array
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
     * @return Document[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDocumentsByUserWithTypeCode(User $user, array $typeCodes): array
    {
        return collect($this->dynamicsCrmClient->getCustomerDocuments($user->crm_id)['documentList'] ?? [])
            ->filter(fn(array $item) => in_array($item['documentType']['code'] ?? '', $typeCodes))
            ->sortByDesc('createdOn')
            ->unique(fn(array $item) => $item['documentType']['code'])
            ->filter(fn(array $item) => !in_array($item['id'], $this->getDeletedDocumentIdsByUser($user)))
            ->map(fn(array $item) => $this->makeDocument($item))
            ->toArray();
    }

    /**
     * @return Document[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDocumentsByCrmIdWithTypeCode(string $UserCrmId, array $typeCodes): array
    {
        return collect($this->dynamicsCrmClient->getCustomerDocuments($UserCrmId)['documentList'] ?? [])
            ->filter(fn(array $item) => in_array($item['documentType']['code'] ?? '', $typeCodes))
            ->sortByDesc('createdOn')
            ->unique(fn(array $item) => $item['documentType']['code'])
            ->map(fn(array $item) => $this->makeDocument($item))
            ->toArray();
    }

    /**
     * @param string $contractId
     * @param array $typeCodes
     * @return Document[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractDocumentsWithTypeCode(string $contractId, array $typeCodes): array
    {
        return collect($this->dynamicsCrmClient->getContractDocuments($contractId)['documentList'] ?? [])
            ->filter(fn(array $item) => in_array($item['documentType']['code'] ?? '', $typeCodes))
            ->sortByDesc('createdOn')
            ->unique(fn(array $item) => $item['documentType']['code'])
            ->map(fn(array $item) => $this->makeDocument($item))
            ->toArray();
    }

    /**
     * @param string $contractId
     * @param array $typeCodes
     * @return Document[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getContractDocumentsWithCode(string $code, string $contractId, array $typeCodes): array
    {
        return collect($this->dynamicsCrmClient->getContractDocumentsWithCode($code, $contractId)['documentList'] ?? [])
            ->filter(fn(array $item) => in_array($item['documentType']['code'] ?? '', $typeCodes))
            ->sortByDesc('createdOn')
            ->unique(fn(array $item) => $item['documentType']['code'])
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
    public function getDocumentIdsByUser(User $user): array
    {
        $documentIds = $this->cache->get($this->getDocumentCacheKeyByUser($user));

        if ($documentIds !== null) {
            return array_filter(
                $documentIds,
                fn(string $id) => !in_array($id, $this->getDeletedDocumentIdsByUser($user))
            );
        }

        return array_map(fn(Document $document) => $document->getId(), $this->getDocumentsByUser($user));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setDocumentsCacheIdsByUser(User $user, array $documents): void
    {
        $this->cache->set(
            $this->getDocumentCacheKeyByUser($user),
            array_map(fn(Document $document) => $document->getId(), $documents),
            now()->addDay()
        );
    }

    private function getDocumentCacheKeyByUser(User $user): string
    {
        return "user.{$user->id}.documents";
    }

    private function getDeletedDocumentIdsByUser(User $user): array
    {
        return $user->deletedDocuments()->pluck('document_id')->toArray();
    }

    public function makeDocument(array $data): Document
    {
        return new Document(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            fileName: $data['fileName'] ?? null,
            mimeType: $data['mimeType'] ?? null,
            type: DocumentType::from(strval($data['documentType']['code'])),
            processingStatus: isset($data['documentProcessingStatus']['code'])?
                DocumentProcessingStatus::from($data['documentProcessingStatus']['code']):null,
            body: $data['documentBody'] ?? null,
            reasonFailure: $data['reasonFailure'] ?? null,
            isDocumentApprove: $data['isDocumentApprove'] ?? null
        );
    }
}
