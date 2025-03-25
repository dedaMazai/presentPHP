<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalReceipts extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'fiscal_receipts';

    /** @inheritdoc */
    protected $fillable = [
        'id',
        'order_id',
        'receipt_id',
        'operation_id',
        'operation_type',
        'receipt_type',
        'receipt_status_code',
        'receipt_status',
        'orig_receipt_id',
        'timestamp',
        'group_code',
        'daemon_code',
        'device_code',
        'ofd_receipt_url',
    ];
}
