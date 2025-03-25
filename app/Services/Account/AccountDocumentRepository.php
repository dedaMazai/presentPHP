<?php

namespace App\Services\Account;

use App\Models\Account\AccountDocument;
use App\Services\Crm\CrmClient;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class AccountDocumentRepository
 *
 * @package App\Services\Account
 */
class AccountDocumentRepository
{
    public function __construct(
        private CrmClient $client,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function getAllByAccountNumber(string $accountNumber): array
    {
        $accountDocuments = $this->cache->get($this->key($accountNumber));
        if ($accountDocuments !== null) {
            return $accountDocuments;
        }

        $data = $this->client->getAccountDocumentsByAccountNumber($accountNumber);
        $accountDocuments = array_map(fn($data) => $this->makeAccountDocument($data), $data);

        $this->cacheAccountDocuments($accountNumber, $accountDocuments);

        return $accountDocuments;
    }

    private function key(string $accountNumber): string
    {
        return "accounts.{$accountNumber}.documents";
    }

    private function makeAccountDocument(array $data): AccountDocument
    {
        return new AccountDocument(
            id: $data['id'],
            name: $data['name'],
            url: $data['url'],
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cacheAccountDocuments(string $accountNumber, array $accountDocuments): void
    {
        $this->cache->set($this->key($accountNumber), $accountDocuments, now()->addDay());
    }
}
