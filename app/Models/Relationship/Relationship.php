<?php

namespace App\Models\Relationship;

use App\Models\Account\AccountInfo;
use App\Models\Role;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Relationship
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $account_number
 * @property Role        $role
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $joint_owner_id
 *
 * @property-read User   $user
 *
 * @package App\Models\Relationship
 */
class Relationship extends Model
{
    use HasFactory;

    /** @inheritdoc */
    protected $fillable = [
        'user_id',
        'account_number',
        'role',
        'joint_owner_id',
    ];

    public function getRoleAttribute(?string $value): ?Role
    {
        return $value !== null ? Role::from($value) : null;
    }

    public function setRoleAttribute(Role $role): void
    {
        $this->attributes['role'] = $role->value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accountInfo(): BelongsTo
    {
        return $this->belongsTo(AccountInfo::class, 'account_number', 'account_number');
    }

    public function scopeByAccountNumber(Builder $query, string $accountNumber): Builder
    {
        return $query->where('account_number', $accountNumber);
    }
}
