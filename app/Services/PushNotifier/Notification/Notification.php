<?php

namespace App\Services\PushNotifier\Notification;

/**
 * Class Notification
 *
 * @package App\Services\PushNotifier\Notification
 */
final class Notification
{
    /**
     * @param Message $message
     * @param Target  ...$target
     *
     * @return static
     */
    public static function create(Message $message, Target ...$target): self
    {
        return new self(target: count($target) === 1 ? $target[0] : $target, message: $message);
    }

    /**
     * @param Target|Target[] $target
     * @param Message         $message
     * @param MetaData|null   $meta
     */
    private function __construct(private array|Target $target, private Message $message, private ?MetaData $meta = null)
    {
        if ($this->meta === null) {
            $this->meta = MetaData::empty();
        }
    }

    /**
     * @return Target|Target[]
     */
    public function target(): array|Target
    {
        return $this->target;
    }

    public function message(): Message
    {
        return $this->message;
    }

    public function meta(): MetaData
    {
        return $this->meta;
    }

    public function hasMeta(): bool
    {
        return $this->meta()->isFilled();
    }

    public function withMeta(array $values): self
    {
        $this->meta()->with($values);

        return $this;
    }

    public function isMulticasting(): bool
    {
        return is_array($this->target);
    }
}
