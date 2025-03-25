<?php

namespace App\Models\Contact;

use App\Components\Sorting\BelongsToSortedManyTrait;
use App\Models\City;
use App\Models\Image;
use App\Models\UkProject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class Contact
 *
 * @property int         $id
 * @property string      $title
 * @property ContactType $type
 * @property int         $icon_image_id
 * @property int|null    $city_id
 * @property string|null $phone
 * @property string|null $email
 * @property float|null  $lat
 * @property float|null  $long
 * @property int|null    $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Image  $iconImage
 * @property-read City   $city
 *
 * @package App\Models\Contact
 */
class Contact extends Model
{
    use BelongsToSortedManyTrait;
    use SortableTrait;

    /** @inheritdoc */
    protected $fillable = [
        'title',
        'type',
        'icon_image_id',
        'city_id',
        'phone',
        'email',
        'lat',
        'long',
    ];

    protected static function booted()
    {
        static::addGlobalScope('ordered', function (Builder $builder) {
            $builder->ordered();
        });
    }

    public function iconImage(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function getTypeAttribute(string $value): ContactType
    {
        return ContactType::from($value);
    }

    public function setTypeAttribute(ContactType $type): void
    {
        $this->attributes['type'] = $type->value;
    }

    public function scopeByUkProject(Builder $query, UkProject $ukProject): Builder
    {
        return $query->whereHasMorph(
            'contactable',
            UkProject::class,
            function (Builder $query) use ($ukProject) {
                $query->where('id', $ukProject->id);
            }
        );
    }

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
