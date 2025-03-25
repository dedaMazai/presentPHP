<?php

namespace App\Services\PushNotifier\Notification;

use function trim;

/**
 * Class MetaData
 *
 * @package App\Services\PushNotifier\Notification
 */
final class MetaData
{
    /** @var string[] */
    private array $items = [];

    public static function empty(): self
    {
        return new self();
    }

    private function __construct(array $values = [])
    {
        $this->with($values);
    }

    public function set(string $name, string $value): self
    {
        $this->items[trim($name)] = trim($value);

        return $this;
    }

    public function with(array $values): self
    {
        foreach ($values as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function isFilled(): bool
    {
        return !$this->isEmpty();
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
