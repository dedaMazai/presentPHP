<?php

namespace App\Services\Claim;

use App\Models\Claim\ClaimMessage\ClaimMessage;
use App\Models\Claim\ClaimMessage\ClaimMessageType;
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Carbon\Carbon;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ClaimMessageRepository
 *
 * @package App\Services\Claim
 */
class ClaimMessageRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @return ClaimMessage[]
     * @throws BadRequestException
     * @throws InvalidArgumentException
     *
     * @throws NotFoundException
     */
    public function getAll(string $claimId, bool $resetCache = false): array
    {
        $messagesData = $this->getFromCache($claimId);
        if ($resetCache) {
            $data = $this->dynamicsCrmClient->getClaimMessagesByClaimId($claimId);
            $messagesData = array_filter(
                $data['messageList'],
                fn($message) => in_array($message['type']['code'], [5, 7, 8]) && !isset($message['documentId']),
                ARRAY_FILTER_USE_BOTH
            );

            $this->cacheMessages($claimId, $messagesData);
        }

        return $this->mapMessages($messagesData);
    }

    public function makeMessage(array $data): ClaimMessage
    {
        $date = (new Carbon($data['messageDate']))
            ->setTimezone('+06:00')
            ->shiftTimezone('+03:00');

        return new ClaimMessage(
            id: $data['id'],
            text: $data['text'],
            messageDate: $date,
            type: $data['type']['code'] == 5 ? ClaimMessageType::client() : ClaimMessageType::manager(),
            senderName: $data['senderName'] ?? null,
            senderPosition: $data['senderPosition'] ?? null,
            isRead: $data['status']['code'] == 1 ? false : true
        );
    }

    /**
     * @param string $claimId
     * @param string $text
     * @param User   $user
     *
     * @return ClaimMessage
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function save(string $claimId, string $text, User $user): ClaimMessage
    {
        $data = $this->dynamicsCrmClient->sendClaimMessage($claimId, $text, $user);

        $this->putMessageToCache($claimId, $data);

        return $this->makeMessage($data);
    }

    public function readMessages(array $messages):array
    {
        $data = $this->dynamicsCrmClient->readMessages($messages);

        return $data;
    }

    public function readDocuments(array $files):array
    {
        $data = $this->dynamicsCrmClient->readDocuments($files);

        return $data;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function putMessageToCache(string $claimId, array $data): void
    {
        $messagesData = $this->getFromCache($claimId);
        $messagesData[] = $data;

        $this->cacheMessages($claimId, $messagesData);
    }

    /**
     * @param string $claimId
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function getFromCache(string $claimId): array
    {
        return $this->cache->get($this->getCacheKey($claimId), []);
    }

    /**
     * @param string $claimId
     * @param array  $data
     *
     * @return void
     * @throws InvalidArgumentException
     */
    private function cacheMessages(string $claimId, array $data): void
    {
        $this->cache->set($this->getCacheKey($claimId), $data);
    }

    /**
     * @param array $messages
     *
     * @return ClaimMessage[]
     */
    private function mapMessages(array $messages): array
    {
        return array_map(fn($data) => $this->makeMessage($data), $messages);
    }

    private function getCacheKey(string $claimId): string
    {
        return "claims.{$claimId}.messages";
    }

    public function getFiles(string $claimId)
    {
        return $this->dynamicsCrmClient->getFilesByClaimId($claimId);
    }
}
