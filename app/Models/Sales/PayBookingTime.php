<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayBookingTime extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'pay_booking_time';

    /** @inheritdoc */
    protected $fillable = [
        'crm_id',
        'customer_id',
        'end_date',
        'time_to_pay',
        'payment_url',
        'contract_id',
        'status',
        'order_id',
        'contract_number',
        'fiscalization_complete',
        'order_creation_time',
        'email',
        'register_do_log',
    ];
}
