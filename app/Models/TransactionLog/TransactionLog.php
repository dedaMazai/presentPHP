<?php

namespace App\Models\TransactionLog;

use App\Models\PaymentMethodType;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TransactionLog
 *
 * @property int                  $id
 * @property int                  $user_id
 * @property string               $account_number
 * @property PaymentMethodType    $payment_method_type
 * @property string               $title
 * @property string|null          $subtitle
 * @property float                $amount
 * @property string|null          $remote_order_id
 * @property TransactionLogStatus $status
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property string|null          $claim_id
 * @property string|null          $claim_number
 * @property string|null          $claim_category_name
 * @property string               $account_service_seller_id
 *
 * @property-read User            $user
 *
 * @package App\Models\TransactionLog
 */
class TransactionLog extends Model
{
    use HasFactory;

    /** @inheritdoc */
    protected $fillable = [
        'user_id',
        'account_number',
        'payment_method_type',
        'title',
        'subtitle',
        'amount',
        'remote_order_id',
        'status',
        'claim_id',
        'claim_number',
        'claim_category_name',
        'account_service_seller_id',
        'psb_order_id',
        'qr_id'
    ];

    public function getPaymentMethodTypeAttribute(string $value): PaymentMethodType
    {
        return PaymentMethodType::from($value);
    }

    public function setPaymentMethodTypeAttribute(PaymentMethodType $paymentMethodType): void
    {
        $this->attributes['payment_method_type'] = $paymentMethodType->value;
    }

    public function getStatusAttribute(string $value): TransactionLogStatus
    {
        return TransactionLogStatus::from($value);
    }

    public function setStatusAttribute(?TransactionLogStatus $status): void
    {
        $this->attributes['status'] = $status->value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByAccountNumber(Builder $query, string $accountNumber): Builder
    {
        return $query->where('account_number', $accountNumber);
    }

    public function scopeByTransactionLogStatus(Builder $query, TransactionLogStatus $status): Builder
    {
        return $query->where('status', $status->value);
    }
}
