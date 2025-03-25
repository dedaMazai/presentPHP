<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Banks
 *
 * @property int            $id
 * @property string         $name
 * @property string         $bank_id
 * @property int            $image_id
 *
 * @property-read Image     $image
 *
 * @package App\Models
 */
class Banks extends Model
{
    use HasFactory;
    use Publicable;

    /** @inheritdoc */
    protected $fillable = [
        'name',
        'bank_id',
        'image_id',
    ];

    public $timestamps = false;

    public function image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'image_id');
    }
}
