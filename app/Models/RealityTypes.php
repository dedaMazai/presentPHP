<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RealityTypes
 *
 * @property int            $id
 * @property string         $reality_name
 * @property int            $reality_id
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @package App\Models
 */
class RealityTypes extends Model
{
    use HasFactory;
    use Publicable;

    /** @inheritdoc */
    protected $fillable = [
        'reality_name',
        'reality_id',
    ];
}
