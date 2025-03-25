<?php

namespace App\Models\Claim\ClaimMessage;

use Carbon\Carbon;

/**
 * Class ClaimMessage
 *
 * @package App\Models\Claim\ClaimMessage
 */
class ClaimMessage
{
    public function __construct(
        private string $id,
        private string $text,
        private Carbon $messageDate,
        private ClaimMessageType $type,
        private ?string $senderName,
        private ?string $senderPosition,
        private bool $isRead
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getMessageDate(): Carbon
    {
        return $this->messageDate;
    }

    public function getType(): ClaimMessageType
    {
        return $this->type;
    }

    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    public function getSenderPosition(): ?string
    {
        return $this->senderPosition;
    }

    public function getIsRead(): bool
    {
        return $this->isRead;
    }
}
