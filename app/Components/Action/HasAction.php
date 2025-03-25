<?php

namespace App\Components\Action;

use App\Models\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class HasAction
 *
 * @property int|null         $action_id
 *
 * @property-read Action|null $action
 *
 * @package App\Components\Action
 */
trait HasAction
{
    public function action(): BelongsTo
    {
        /** @var $this Model */
        return $this->belongsTo(Action::class);
    }

    public function updateAction(?string $type, array $payload): void
    {
        /** @var $this Model */
        if (empty($type)) {
            if (empty($this->action)) {
                return;
            }

            $this->action()->delete();
            return;
        }

        if (empty($this->action)) {
            $action = Action::create([
                'type' => $type,
                'payload' => $payload,
            ]);
            $this->action()->associate($action);
            $this->saveOrFail();

            return;
        }

        $this->action->update([
            'type' => $type,
            'payload' => $payload,
        ]);
    }
}
