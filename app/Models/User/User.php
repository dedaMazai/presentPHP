<?php

namespace App\Models\User;

use App\Components\Views\Traits\CanView;
use App\Models\Account\Account;
use App\Models\Document\DeletedDocument;
use App\Models\Relationship\Relationship;
use App\Models\Account\AccountNumbers;
use App\Models\Role;
use App\Models\Sales\Deal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Contracts\HasApiTokens;
use Laravel\Sanctum\HasApiTokens as HasApiTokensTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 *
 * @property int                     $id
 * @property string                  $phone
 * @property string                  $email
 * @property string                  $first_name
 * @property string                  $last_name
 * @property string|null             $middle_name
 * @property Carbon                  $birth_date
 * @property string                  $password
 * @property string[]                $enabled_notifications
 * @property string|null             $crm_id
 * @property string|null             $push_token
 * @property Carbon|null             $created_at
 * @property Carbon|null             $updated_at
 * @property Carbon|null             $deleted_at
 * @property string[]                $subscribed_to_push_topics
 * @property boolean[]               $manager_control
 * @property boolean                 status
 *
 * @property-read object             $token
 * @property-read Relationship[]     $relationships
 * @property-read DeletedDocument[]  $deletedDocuments
 * @property-read FavoriteProperty[] $favoriteProperties
 * @property-read Deal[]             $deals
 *
 * @package App\Models\User
 */
class User extends Model implements HasApiTokens
{
    use HasFactory;
    use SoftDeletes;
    use CanView;
    use HasApiTokensTrait;

    /** @inheritdoc */
    protected $casts = [
        'enabled_notifications' => 'array',
        'subscribed_to_push_topics' => 'array',
        'birth_date' => 'datetime',
    ];

    /** @inheritdoc */
    protected $fillable = [
        'id',
        'crm_id',
        'phone',
        'email',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'push_token',
        'subscribed_to_push_topics',
        'manager_control',
        'status'
    ];

    protected $hidden = [
        'password',
    ];

    public function getFullName(): string
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }

    public function hasPassword(): bool
    {
        return !empty($this->password);
    }

    public function changePassword(string $password): void
    {
        $this->password = Hash::make($password);
    }

    /** @inheritdoc */
    protected static function booted()
    {
        static::creating(function (self $invite) {
            $invite->enabled_notifications = NotificationChannel::toValues();
        });
    }

    public function toggleNotification(NotificationChannel $channel): void
    {
        if (in_array($channel->value, $this->enabled_notifications)) {
            $this->enabled_notifications = array_values(array_diff($this->enabled_notifications, [$channel->value]));
        } else {
            $this->enabled_notifications = [...$this->enabled_notifications, $channel->value];
        }

        $this->save();
    }

    public function canReceiveObjectNewsPush(): bool
    {
        return in_array(NotificationChannel::pushNewsObject(), $this->enabled_notifications);
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(Relationship::class);
    }

    public function accountNumbers(): HasMany
    {
        return $this->hasMany(AccountNumbers::class);
    }

    public function deletedDocuments(): HasMany
    {
        return $this->hasMany(DeletedDocument::class);
    }

    public function deleteDocument(string $documentId): void
    {
        $this->deletedDocuments()->create(['document_id' => $documentId]);
    }

    public function hasAccountRight(string $accountNumber): bool
    {
        return $this->relationships()
            ->where('account_number', $accountNumber)
            ->exists();
    }

    public function getRoleByAccount(Account $account): ?Relationship
    {
        /** @var Relationship $relationship */
        $relationship = $this->relationships()
            ->where('account_number', $account->getNumber())
            ->first();

        return $relationship;
    }

    public function hasOwnerAccountRight(string $accountNumber): bool
    {
        return $this->accountNumbers()
            ->where('account_number', $accountNumber)
            ->where('role', Role::owner())
            ->exists();
    }

    public function favoriteProperties(): HasMany
    {
        return $this->hasMany(FavoriteProperty::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function ban(): HasMany
    {
        return $this->hasMany(BanPhone::class, 'phone_number', 'phone');
    }
}
