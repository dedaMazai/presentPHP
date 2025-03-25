<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SupportTopics
 *
 * @property int            $id
 * @property string         $name
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @package App\Models
 */
class SupportTopics extends Model
{
    use HasFactory;
    use Publicable;

    /** @inheritdoc */
    protected $fillable = [
        'name',
        'is_published',
    ];

    protected static function booted()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->published();
        });
    }
}
