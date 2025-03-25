<?php

namespace App\Models\Sales;

use App\Models\Sales\Demand\DemandBookingType;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Deal
 *
 * @property int               $id
 * @property int               $user_id
 * @property string            $demand_id
 * @property DemandStatus      $demand_status
 * @property DemandBookingType $demand_booking_type
 * @property Carbon            $initial_begin_date
 * @property Carbon            $initial_end_date
 * @property string            $property_id
 * @property boolean           $is_escrow
 * @property boolean           $is_escrow_bank_client
 * @property Carbon|null       $created_at
 * @property Carbon|null       $updated_at
 * @property string|null       $current_step
 * @property int|null          $project_id
 * @property boolean           $is_booking_paid
 * @property string|null       $mortgage_demand_id
 * @property Carbon|null       $contract_read_at
 *
 * @property-read User         $user
 *
 * @package App\Models\Sales
 */
class Deal extends Model
{
    use HasFactory;

    /** @inheritdoc */
    protected $fillable = [
        'user_id',
        'demand_id',
        'demand_status',
        'demand_booking_type',
        'property_id',
        'is_escrow',
        'is_escrow_bank_client',
        'current_step',
        'project_id',
        'is_booking_paid',
        'mortgage_demand_id',
        'contract_read_at',
        'initial_begin_date',
        'initial_end_date',
    ];

    public function getDemandStatusAttribute(string $value): DemandStatus
    {
        return DemandStatus::from($value);
    }

    public function setDemandStatusAttribute(DemandStatus $status): void
    {
        $this->attributes['demand_status'] = $status->value;
    }

    public function getDemandBookingTypeAttribute(string $value): DemandBookingType
    {
        return DemandBookingType::from($value);
    }

    public function setDemandBookingTypeAttribute(DemandBookingType $bookingType): void
    {
        $this->attributes['demand_booking_type'] = $bookingType->value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByDemandId(Builder $query, string $demandId): Builder
    {
        return $query->where('demand_id', $demandId);
    }

    public function scopeByDemandStatus(Builder $query, DemandStatus $status): Builder
    {
        return $query->where('demand_status', $status);
    }

    public function scopeByDemandBookingType(Builder $query, DemandBookingType $bookingType): Builder
    {
        return $query->where('demand_booking_type', $bookingType);
    }

    public function scopeBookingPaid(Builder $query): Builder
    {
        return $query->where('is_booking_paid', true);
    }

    public function scopeBookingNotPaid(Builder $query): Builder
    {
        return $query->where('is_booking_paid', false);
    }
}
