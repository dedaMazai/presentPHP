<?php

namespace App\Models\Relationship;

use App\Models\Role;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class RelationshipInvite
 *
 * @property int         $id
 * @property string      $account_number
 * @property int         $owner_id
 * @property Role        $role
 * @property string      $first_name
 * @property string      $last_name
 * @property string      $phone
 * @property Carbon      $birth_date
 * @property Carbon|null $accepted_at
 * @property int|null    $accepted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read User   $owner
 *
 * @package App\Models\Relationship
 */
class RelationshipInvite extends Model
{
    use HasFactory;

    /** @inheritdoc */
    protected $fillable = [
        'account_number',
        'owner_id',
        'role',
        'first_name',
        'last_name',
        'phone',
        'birth_date',
    ];

    /** @inheritdoc */
    protected $casts = [
        'accepted_at' => 'datetime',
        'birth_date' => 'date:Y-m-d',
    ];

    public function getRoleAttribute(?string $value): ?Role
    {
        return $value !== null ? Role::from($value) : null;
    }

    public function setRoleAttribute(Role $role): void
    {
        $this->attributes['role'] = $role->value;
    }

    public function scopeByAccountNumber(Builder $query, string $accountNumber): Builder
    {
        return $query->where('account_number', $accountNumber);
    }

    public function scopeByPhone(Builder $query, string $phone): Builder
    {
        return $query->where('phone', $phone);
    }

    public function scopeUnaccepted(Builder $query): Builder
    {
        return $query->whereNull('accepted_at');
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
