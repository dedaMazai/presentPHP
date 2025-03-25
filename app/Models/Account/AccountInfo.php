<?php

namespace App\Models\Account;

use App\Components\Publication\Publicable;
use App\Models\Relationship\Relationship;
use App\Models\UkProject;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class AccountInfo
 *
 * @property string            $account_number
 * @property int|null          $uk_project_id
 * @property AccountRealtyType $realty_type
 * @property Carbon|null       $created_at
 * @property Carbon|null       $updated_at
 * @property string            $build_zid
 *
 * @property-read UkProject    $ukProject
 *
 * @package App\Models\Account
 */
class AccountInfo extends Model
{
    use HasFactory;
    use Publicable;

    /** @inheritdoc */
    public $incrementing = false;

    /** @inheritdoc */
    protected $table = 'account_info';

    /** @inheritdoc */
    protected $primaryKey = 'account_number';

    /** @inheritdoc */
    protected $keyType = 'string';

    /** @inheritdoc */
    protected $fillable = [
        'account_number',
        'uk_project_id',
        'realty_type',
        'build_zid',
        'build_id',
        'address',
        'balance',
        'services_debt',
        'not_paid_months',
        'is_meter_enter_period_active',
        'project_crm_1c_id',
        'meter_enter_period_start',
        'meter_enter_period_end',
        'address_id',
        'service_seller_id',
        'address_number',
        'project_name',
        'project_id',
        'uk_emergency_claim_phone',
        'classifier_uk_id',
    ];

    public function getRealtyTypeAttribute(string $value): AccountRealtyType
    {
        return AccountRealtyType::from($value);
    }

    public function setRealtyTypeAttribute(AccountRealtyType $realtyType): void
    {
        $this->attributes['realty_type'] = $realtyType->value;
    }

    public function ukProject(): BelongsTo
    {
        return $this->belongsTo(UkProject::class);
    }

    public function accountNumbers(): HasMany
    {
        return $this->hasMany(AccountNumbers::class, 'account_number', 'account_number');
    }
}
