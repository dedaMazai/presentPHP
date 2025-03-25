<?php

namespace App\Models\Sales\Bank;

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
 * Class BankInfo
 *
 * @property int         $id
 * @property boolean     $is_published
 * @property string      $title
 * @property int         $logo_image_id
 * @property float       $price
 * @property string|null $link
 * @property string      $crm_id
 * @property int|null    $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property BankType    $type
 *
 * @property-read Image  $logoImage
 *
 * @package App\Models
 */
class BankInfo extends Model implements Sortable
{
    use HasFactory;
    use Publicable;
    use SortableTrait;

    /** @inheritdoc */
    protected $table = 'bank_info';

    /** @inheritdoc */
    protected $fillable = [
        'is_published',
        'title',
        'logo_image_id',
        'price',
        'link',
        'crm_id',
        'type',
    ];

    protected $casts = [
        'price' => 'float',
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

    public function getTypeAttribute(string $value): BankType
    {
        return BankType::from($value);
    }

    public function setTypeAttribute(BankType $type): void
    {
        $this->attributes['type'] = $type->value;
    }

    public function logoImage(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function scopeByType(Builder $query, BankType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function scopeByCrmId(Builder $query, string $crmId): Builder
    {
        return $query->where('crm_id', $crmId);
    }
}
