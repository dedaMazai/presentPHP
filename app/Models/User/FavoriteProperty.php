<?php

namespace App\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FavoriteProperty
 *
 * @property int         $user_id
 * @property string      $property_crm_id
 * @property string|null $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\User
 */
class FavoriteProperty extends Model
{
    /** @inheritdoc */
    public $incrementing = false;

    /** @inheritdoc */
    protected $primaryKey = ['user_id', 'property_crm_id'];

    /** @inheritdoc */
    protected $fillable = [
        'user_id',
        'property_crm_id',
        'url',
    ];
}
