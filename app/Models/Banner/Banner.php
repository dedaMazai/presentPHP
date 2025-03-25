<?php

namespace App\Models\Banner;

use App\Components\Publication\Publicable;
use App\Models\Image;
use App\Models\News\News;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class Banner
 *
 * @property int         $id
 * @property boolean     $is_published
 * @property BannerPlace $place
 * @property int         $image_id
 * @property int|null    $news_id
 * @property string|null $category_crm_id
 * @property string|null $url
 * @property int|null    $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Image  $image
 * @property-read News   $news
 *
 * @package App\Models\Banner
 */
class Banner extends Model
{
    use HasFactory;
    use Publicable;
    use SortableTrait;

    /** @inheritdoc */
    protected $fillable = [
        'is_published',
        'place',
        'image_id',
        'news_id',
        'category_crm_id',
        'url',
        'order',
        'start_date',
        'end_date'
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

    public function getPlaceAttribute(string $value): BannerPlace
    {
        return BannerPlace::from($value);
    }

    public function setPlaceAttribute(BannerPlace $place): void
    {
        $this->attributes['place'] = $place->value;
    }

    public function scopeByPlace(Builder $query, BannerPlace $place): Builder
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
