<?php

namespace App\Services\PushNotifier\Notification;

use function trim;

/**
 * Class Message
 *
 * @package App\Services\PushNotifier\Notification
 */
final class Message
{
    public static function create(
        string $title,
        string $body = '',
        ?string $image = null,
        ?string $clickAction = null
    ): self {
        return new self($title, $body, $image, $clickAction);
    }

    private function __construct(
        private string $title,
        private string $body,
        private ?string $image,
        private ?string $clickAction
    ) {
        $this->title = trim($title);
        $this->body = trim($body);
        $this->image = $image ? trim($image) : $image;
        $this->clickAction = $clickAction ? trim($clickAction) : $clickAction;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function image(): ?string
    {
        return $this->image;
    }

    public function clickAction(): ?string
    {
        return $this->clickAction;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title(),
            'body' => $this->body(),
            'image' => $this->image(),
            'click_action' => $this->clickAction(),
        ];
    }
}
