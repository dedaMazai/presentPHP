<?php

namespace App\Models\Project;

use App\Components\Publication\Publicable;
use App\Models\Article\Article;
use App\Models\Image;
use App\Models\MortgageProgram;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class Project
 *
 * @property int                    $id
 * @property int                    $type_id
 * @property boolean                $is_published
 * @property string                 $name
 * @property int                    $showcase_image_id
 * @property int                    $main_image_id
 * @property int|null               $map_image_id
 * @property string|null            $metro
 * @property string|null            $metro_color
 * @property array                  $crm_ids
 * @property int|null               $mortgage_calculator_id
 * @property float                  $lat
 * @property float                  $long
 * @property string|null            $office_phone
 * @property string|null            $office_address
 * @property float|null             $office_lat
 * @property float|null             $office_long
 * @property string|null            $office_work_hours
 * @property Carbon|null            $created_at
 * @property Carbon|null            $updated_at
 * @property array                  $property_type_params
 * @property string                 $color
 * @property string|null            $description
 * @property int|null               $order
 * @property int|null               $city_id
 * @property array                  $mortgage_types
 * @property array                  $payroll_bank_programs
 * @property float|null             $mortgage_min_property_price
 * @property float|null             $mortgage_max_property_price
 * @property float|null             $min_property_price
 * @property float|null             $max_property_price
 * @property array                  $booking_property
 * @property boolean                $is_premium
 *
 * @property-read ProjectType       $type
 * @property-read Image             $showcaseImage
 * @property-read Image             $mainImage
 * @property-read Image             $mapImage
 * @property-read Image[]           $images
 * @property-read Article[]         $articles
 * @property-read MortgageProgram[] $mortgagePrograms
 *
 * @package App\Models\Project
 */
class Project extends Model
{
    use HasFactory;
    use Publicable;
    use SortableTrait;

    /** @inheritdoc */
    protected $casts = [
        'property_type_params' => AsArrayObject::class,
        'crm_ids' => AsArrayObject::class,
        'mortgage_types' => AsArrayObject::class,
        'payroll_bank_programs' => AsArrayObject::class,
        'mortgage_min_property_price' => 'float',
        'mortgage_max_property_price' => 'float',
        'min_property_price' => 'float',
        'booking_property' => 'array',
    ];

    /** @inheritdoc */
    protected $fillable = [
        'type_id',
        'is_published',
        'name',
        'showcase_image_id',
        'main_image_id',
        'map_image_id',
        'metro',
        'metro_color',
        'crm_ids',
        'mortgage_calculator_id',
        'lat',
        'long',
        'office_phone',
        'office_address',
        'office_lat',
        'office_long',
        'office_work_hours',
        'property_type_params',
        'color',
        'description',
        'order',
        'city_id',
        'mortgage_types',
        'payroll_bank_programs',
        'mortgage_min_property_price',
        'mortgage_max_property_price',
        'min_property_price',
        'max_property_price',
        'booking_property',
        'url_memo'
    ];

    /** @inheritdoc */
    protected $appends = [
        'image_ids',
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

    public function type(): HasOne
    {
        return $this->hasOne(ProjectType::class, 'id', 'type_id');
    }

    public function showcaseImage(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'showcase_image_id');
    }

    public function mainImage(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'main_image_id');
    }

    public function mapImage(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'map_image_id');
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'project_image');
    }

    public function articles(): MorphMany
    {
        return $this->morphMany(Article::class, 'articlable');
    }

    public function mortgagePrograms(): HasMany
    {
        return $this->hasMany(MortgageProgram::class);
    }

    public function scopeByType(Builder $query, ProjectType $type): Builder
    {
        return $query->where('type_id', $type->id);
    }

    public function getImageIdsAttribute(): array
    {
        $imageIds = [];

        foreach ($this->images as $image) {
            $imageIds[] = $image->id;
        }

        return $imageIds;
    }
}
