<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Finishing
 *
 * @property int            $id
 * @property boolean        $is_published
 * @property string         $finishing_id
 * @property string|null    $description
 *
 * @property-read Image[]     $images
 *
 * @package App\Models
 */
class Finishing extends Model
{
    use HasFactory;
    use Publicable;

    /** @inheritdoc */
    protected $appends = [
        'images_id',
    ];
//push
    /** @inheritdoc */
    protected $fillable = [
        'is_published',
        'finishing_id',
        'description',
        'name',
        'catalog_url',
    ];
//push
    public $timestamps = false;

    protected static function booted()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->published();
        });
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'finishing_images');
    }

    public function getImagesIdAttribute(): array
    {
        $images_id = [];

        foreach ($this->images as $image) {
            $images_id[] = $image->id;
        }

        return $images_id;
    }
}
