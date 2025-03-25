<?php

namespace App\Models\Ad;

use App\Components\Publication\Publicable;
use App\Models\Image;
use App\Models\News\News;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Ad
 *
 * @property int         $id
 * @property boolean     $is_published
 * @property AdPlace     $place
 * @property string      $title
 * @property string|null $subtitle
 * @property int|null    $image_id
 * @property int|null    $news_id
 * @property string|null $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Image  $image
 * @property-read News   $news
 *
 * @package App\Models\Ad
 */
class Ad extends Model
{
    use HasFactory;
    use Publicable;

    /** @inheritdoc */
    protected $fillable = [
        'id',
        'is_published',
        'place',
        'title',
        'subtitle',
        'image_id',
        'news_id',
        'url',
        'start_date',
        'end_date',
    ];

    protected static function booted()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->published();
        });
    }

    public function getPlaceAttribute(string $value): AdPlace
    {
        return AdPlace::from($value);
    }

    public function setPlaceAttribute(AdPlace $place): void
    {
        $this->attributes['place'] = $place->value;
    }

    public function scopeByPlace(Builder $query, AdPlace $place): Builder
    {
        return $query->where('place', $place->value);
    }

    public function scopeByActive(Builder $query): Builder
    {
        $currentTime = Carbon::now()->toDateTimeString();

        return $query->where('start_date', '<=', $currentTime)
            ->where('end_date', '>=', $currentTime)->orWhere('end_date', null);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
