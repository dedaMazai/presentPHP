<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeletingReason
 *
 * @property int    $id
 * @property string $value
 * @property string $title
 *
 * @package App\Models\User
 */
class DeletingReason extends Model
{
    use HasFactory;

    public $timestamps = false;

    /** @inheritdoc */
    protected $fillable = [
        'value',
        'title',
    ];
}
