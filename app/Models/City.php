<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class City
 *
 * @property int    $id
 * @property string $name
 *
 * @package App\Models
 */
class City extends Model
{
    use HasFactory;

    public $timestamps = false;

    /** @inheritdoc */
    protected $fillable = [
        'name'
    ];
}
