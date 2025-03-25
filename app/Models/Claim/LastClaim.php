<?php

namespace App\Models\Claim;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LastClaim
 *
 * @property string $account_number
 * @property string $claim_id
 * @property Carbon $claim_created_at
 *
 * @package App\Models
 */
class LastClaim extends Model
{
    /** @inheritdoc */
    public $incrementing = false;

    /** @inheritdoc */
    public $timestamps = false;

    /** @inheritdoc */
    protected $primaryKey = 'account_number';

    /** @inheritdoc */
    protected $keyType = 'string';

    /** @inheritdoc */
    protected $fillable = [
        'account_number',
        'claim_id',
        'claim_created_at',
    ];
}
