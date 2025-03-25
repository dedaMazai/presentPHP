<?php

namespace App\Models\Meter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MeterName
 *
 * @property int    $id
 * @property string $account_number
 * @property string $meter_id
 * @property string $name
 *
 * @package App\Models\Meter
 */
class MeterName extends Model
{
    use HasFactory;

    /** @inheritdoc */
    protected $fillable = [
        'account_number',
        'meter_id',
        'name',
    ];
}
