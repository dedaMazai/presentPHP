<?php

namespace App\Models\Notification;

use App\Components\Action\HasAction;
use App\Models\Account\AccountInfo;
use App\Models\Action;
use App\Models\Sales\Deal;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Components\Views\Traits\Viewable;
use App\Components\Views\Viewable as ViewableContract;

/**
 * Class Notification
 *
 * @property int                         $id
 * @property string                      $title
 * @property string|null                 $text
 * @property NotificationType            $type
 * @property NotificationDestinationType $destination_type
 * @property array                       $destination_type_payload
 * @property Carbon|null                 $created_at
 * @property Carbon|null                 $updated_at
 *
 * @property-read User[]                 $recipients
 * @property-read Action|null            $action
 *
 * @package App\Models\Notification
 */
class Notification extends Model implements ViewableContract
{
    use HasAction;
    use Viewable;
    use HasFactory;

    /** @inheritdoc */
    protected $fillable = [
        'title',
        'text',
        'type',
        'destination_type',
        'action_id',
        'destination_type_payload',
    ];

    protected $casts = [
        'destination_type_payload' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope('latest', fn(Builder $builder) => $builder->latest());
    }

    public function getTypeAttribute(string $value): NotificationType
    {
        return NotificationType::from($value);
    }

    public function setTypeAttribute(NotificationType $type): void
    {
        $this->attributes['type'] = $type->value;
    }

    public function getDestinationTypeAttribute(string $value): NotificationDestinationType
    {
        return NotificationDestinationType::from($value);
    }

    public function setDestinationTypeAttribute(NotificationDestinationType $type): void
    {
        $this->attributes['destination_type'] = $type->value;
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user', 'notification_id', 'user_id');
    }

    public function scopeForClient(Builder $builder): Builder
    {
        return $builder->whereIn(
            'type',
            NotificationType::clientTypes()
        );
    }

    public function scopeForAllUsers(Builder $builder): Builder
    {
        return $builder->forClient()->where(
            'destination_type',
            NotificationDestinationType::allUsers()->value
        );
    }

    public function scopeForUser(Builder $builder, User $user): Builder
    {
        $accountsNumbers = $user->relationships()->pluck('account_number')->toArray();
// phpcs:disable
        $usersUkId = AccountInfo::whereIn('account_number', $accountsNumbers)->distinct()->pluck('uk_project_id')->toArray();
        $usersRealityType = AccountInfo::whereIn('account_number', $accountsNumbers)->distinct()->pluck('realty_type')->toArray();
        $usersDeal = Deal::where('user_id', $user->id)->distinct()->pluck('project_id')->toArray();
        $buildings_id = AccountInfo::whereIn('account_number', $accountsNumbers)->distinct()->pluck('build_id')->toArray();
// phpcs:enable
        return $builder->forClient()->where(
            'destination_type',
            NotificationDestinationType::allUsers()->value
        )
            ->orWhere(function ($query) use ($buildings_id) {
                foreach ($buildings_id as $building_id) {
                    $query->orWhereJsonContains(
                        'destination_type_payload',
                        ['buildings_id' => [$building_id]]
                    );
                }
            })
//            ->orWhere(function ($query) use ($usersUkId) {
//                foreach ($usersUkId as $userUkId) {
//                    $query->orWhereJsonContains(
//                        'destination_type_payload',
//                        ['uk_project_ids' => []]
//                    )
//                        ->orWhereJsonContains(
//                            'destination_type_payload',
//                            ['uk_project_ids' => $userUkId]
//                        )
//                        ->orWhereJsonContains(
//                            'destination_type_payload',
//                            ['uk_project_ids' => [$userUkId]]
//                        );
//                }
//            })
            ->orWhere(function ($query) use ($usersRealityType) {
                foreach ($usersRealityType as $userRealityType) {
                    $query->orWhereJsonContains(
                        'destination_type_payload',
                        ['account_realty_types' => []]
                    )
                        ->orWhereJsonContains(
                            'destination_type_payload',
                            ['account_realty_types' => [[$userRealityType]]]
                        );
                }
            })
            ->orWhere(function ($query) use ($usersDeal) {
                foreach ($usersDeal as $userDeal) {
                    $query->orWhereJsonContains(
                        'destination_type_payload',
                        ['project_ids' => []]
                    )
                        ->orWhereJsonContains(
                            'destination_type_payload',
                            ['project_ids' => [$userDeal]]
                        );
                }
            })
            ->orWhereHas(
                'recipients',
                fn(Builder $q) => $q->where('user_id', $user->id)
            );
    }
}
