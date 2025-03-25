<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GroupingRealityTypes
 *
 * @property int            $id
 * @property string         $group_reality_name
 * @property array          $group_reality_ids
 * @property int|null       $order
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @package App\Models
 */
class GroupingRealityTypes extends Model
{
    use HasFactory;
    use Publicable;

    protected $casts = [
        'group_reality_ids' => 'array',
    ];

    /** @inheritdoc */
    protected $fillable = [
        'group_reality_name',
        'group_reality_ids',
    ];
}
