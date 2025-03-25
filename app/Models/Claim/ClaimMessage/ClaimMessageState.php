<?php

namespace App\Models\Claim\ClaimMessage;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ClaimMessageState
 *
 * @property int    $user_id
 * @property string $claim_id
 * @property bool   $has_new_messages
 *
 * @package App\Models\Claim
 */
class ClaimMessageState extends Model
{
    /** @inheritdoc */
    protected $fillable = [
        'user_id',
        'claim_id',
        'has_new_messages',
    ];

    /** @inheritdoc */
    protected $primaryKey = 'claim_id';

    public function scopeByUserAndClaimId(Builder $query, User $user, string $claimId): Builder
    {
        return $query->where(['user_id' => $user->id, 'claim_id' => $claimId]);
    }
}
