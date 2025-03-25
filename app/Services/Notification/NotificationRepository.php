<?php

namespace App\Services\Notification;

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
class NotificationRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
    ) {
    }

    public function getNewCommunication($userId):bool
    {
        try {
            return $this->dynamicsCrmClient->getExistUnread($userId);
        } catch (Exception) {
            return false;
        }
    }

    public function getNewMessagesState($userId):bool
    {
        try {
            return $this->dynamicsCrmClient->getExistUnread($userId);
        } catch (Exception) {
            return false;
        }
    }
}
