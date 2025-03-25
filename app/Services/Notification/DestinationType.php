<?php

namespace App\Services\Notification;

/**
 * Class DestinationType
 *
 * @package App\Services\Notification
 */
class DestinationType
{
    /**
     * DestinationType constructor.
     *
     * @param string                        $name
     * @param string                        $label
     * @param bool                          $availableForAdmin
     * @param string[]                      $availableActions
     * @param DestinationTypePayloadParam[] $payloadParams
     */
    public function __construct(
        public string $name,
        public string $label,
        public bool $availableForAdmin,
        public array $availableActions,
        public array $payloadParams = [],
    ) {
    }

    public function hasPayloadParams(): bool
    {
        return !empty($this->payloadParams);
    }
}
