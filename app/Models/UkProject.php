<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use App\Models\Building\Building;
use App\Models\Article\Article;
use App\Models\Contact\Contact;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class UkProject
 *
 * @property int            $id
 * @property string         $name
 * @property string|null    $crm_1c_id
 * @property int            $image_id
 * @property int            $market_image_id
 * @property string         $description
 * @property string         $postcode
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @property-read Image     $image
 * @property-read Article[] $articles
 *
 * @package App\Models
 */
class UkProject extends Model
{
    use HasFactory;
    use Publicable;

    /** @inheritdoc */
    protected $fillable = [
        'is_published',
        'name',
        'crm_1c_id',
        'image_id',
        'market_image_id',
        'description',
        'postcode',
        'uk_emergency_claim_phone'
    ];

    protected static function booted()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->published();
        });
    }

    public function image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function marketImage(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'market_image_id');
    }

    public function buildings(): MorphMany
    {
        return $this->morphMany(Building::class, 'project');
    }

    public function articles(): MorphMany
    {
        return $this->morphMany(Article::class, 'articlable');
    }

    public function scopeByCrm1CId(Builder $query, string $crm1CId): Builder
    {
        return $query->where('crm_1c_id', $crm1CId);
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }
}
