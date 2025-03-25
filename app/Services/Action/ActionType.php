<?php

namespace App\Services\Action;

/**
 * Class ActionType
 *
 * @package App\Services\Action
 */
class ActionType
{
    /**
     * ActionType constructor.
     *
     * @param string               $name
     * @param string               $label
     * @param ActionPayloadParam[] $payloadParams
     */
    public function __construct(
        public string $name,
        public string $label,
        public array $payloadParams = [],
    ) {
    }

    public function hasParams(): bool
    {
        return !empty($this->payloadParams);
    }
}
