<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class RealityTypes
 *
 * @property int            $id
 * @property string         $role_name
 * @property int            $role_code
 * @property int|null       $order
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @package App\Models
 */
class ClientRoleTypes extends Model
{
    use HasFactory;
    use Publicable;

    /** @inheritdoc */
    protected $fillable = [
        'role_name',
        'role_code',
    ];
}
