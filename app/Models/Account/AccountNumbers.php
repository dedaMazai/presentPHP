<?php

namespace App\Models\Account;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Deal
 *
 * @property string         $account_number
 * @property string         $role
 * @property string         $user_id
 *
 * @property-read User      $user
 *
 * @package App\Models\Accounts
 */
class AccountNumbers extends Model
{
    use HasFactory;

    /** @inheritdoc */
    public $incrementing = false;
    public $timestamps = false;

    /** @inheritdoc */
    protected $table = 'account_numbers';

    /** @inheritdoc */
    protected $fillable = [
        'account_number',
        'role',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accountInfo(): BelongsTo
    {
        return $this->belongsTo(AccountInfo::class, 'account_number');
    }
}
