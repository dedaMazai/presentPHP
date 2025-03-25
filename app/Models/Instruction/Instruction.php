<?php

namespace App\Models\Instruction;

use App\Components\Publication\Publicable;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class Instruction
 *
 * @property int         $id
 * @property boolean     $is_published
 * @property string      $title
 * @property int         $image_id
 * @property string|null $text
 * @property int|null    $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Image  $image
 *
 * @package App\Models\Instruction
 */
class Instruction extends Model implements Sortable
{
    use HasFactory;
    use Publicable;
    use SortableTrait;

    /** @inheritdoc */
    protected $fillable = [
        'is_published',
        'title',
        'image_id',
        'text',
        'order',
    ];

    protected static function booted()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->published();
        });

        static::addGlobalScope('ordered', function (Builder $builder) {
            $builder->ordered();
        });
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }
}
