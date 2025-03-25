<?php

namespace App\Models\Claim;

use App\Models\Claim\ClaimFilter\ClaimFilterStatus;
use Spatie\Enum\Enum;

/**
 * Class ClaimStatus
 *
 * @method static self new()
 * @method static self accepted()
 * @method static self planned()
 * @method static self executed()
 * @method static self postponed()
 * @method static self reopened()
 * @method static self closedByManager()
 * @method static self cancelledByManager()
 * @method static self closedByClient()
 * @method static self cancelledByClient()
 * @method static self closedByTime()
 * @method static self notAccepted()
 *
 * @package App\Models\Claim
 */
class ClaimStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'new' => '1',
            'accepted' => '100000000',
            'planned' => '100000001',
            'executed' => '100000002',
            'postponed' => '100000003',
            'reopened' => '100000004',
            'closedByManager' => '2',
            'cancelledByManager' => '100000006',
            'closedByClient' => '100000007',
            'cancelledByClient' => '100000008',
            'closedByTime' => '100000009',
            'notAccepted' => '100000010'
        ];
    }

    protected static function labels(): array
    {
        return [
            'new' => 'Новая',
            'accepted' => 'Принята',
            'planned' => 'Запланирована',
            'executed' => 'Выполнена',
            'postponed' => 'Отложена',
            'reopened' => 'Переоткрыта',
            'closedByManager' => 'Закрыта менеджером',
            'cancelledByManager' => 'Отменена менеджером',
            'closedByClient' => 'Закрыта клиентом',
            'cancelledByClient' => 'Отменена клиентом',
            'closedByTime' => 'Закрыта по времени',
            'notAccepted' => 'Завершено'
        ];
    }

    public function getFilterStatus(): ClaimFilterStatus
    {
        if ($this->equals(ClaimStatus::new())) {
            return ClaimFilterStatus::new();
        } elseif ($this->equals(ClaimStatus::accepted())) {
            return ClaimFilterStatus::beingProcessed();
        } elseif ($this->equals(ClaimStatus::planned(), ClaimStatus::executed(), ClaimStatus::postponed())) {
            return ClaimFilterStatus::inProgress();
        } elseif ($this->equals(
            ClaimStatus::closedByManager(),
            ClaimStatus::closedByClient(),
            ClaimStatus::closedByTime(),
        )) {
            return ClaimFilterStatus::closed();
        } elseif ($this->equals(ClaimStatus::cancelledByManager(), ClaimStatus::cancelledByClient())) {
            return ClaimFilterStatus::cancelled();
        } elseif ($this->equals(ClaimStatus::reopened())) {
            return ClaimFilterStatus::reopened();
        } elseif ($this->equals(ClaimStatus::notAccepted())) {
            return ClaimFilterStatus::notAccepted();
        }

        return ClaimFilterStatus::new();
    }

    public function isCancelable(): bool
    {
        return $this->equals(
            ClaimStatus::new(),
            ClaimStatus::accepted(),
            ClaimStatus::reopened(),
        );
    }
}
