<?php

namespace App\Models\News;

use App\Components\Publication\Publicable;
use App\Components\Sorting\BelongsToSortedMany;
use App\Components\Sorting\BelongsToSortedManyTrait;
use App\Models\Account\Account;
use App\Models\ContentItem\ContentItem;
use App\Models\Image;
use App\Models\Notification\NotificationDestinationType;
use App\Models\UkProject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class News
 *
 * @property int                         $id
 * @property boolean                     $is_published
 * @property NewsType                    $type
 * @property NewsCategory                $category
 * @property int|null                    $uk_project_id
 * @property string                      $title
 * @property string|null                 $description
 * @property string|null                 $tag
 * @property string|null                 $url
 * @property int|null                    $preview_image_id
 * @property string                      $preview_text
 * @property bool                        $should_send_notification
 * @property bool                        $is_sent
 * @property Carbon|null                 $created_at
 * @property Carbon|null                 $updated_at
 * @property NotificationDestinationType $destination
 * @property array|null                  $buildings_id
 *
 * @property-read UkProject              $ukProject
 * @property-read ContentItem[]          $contentItems
 * @property-read Image                  $previewImage
 *
 * @package App\Models\News
 */
class News extends Model
{
    use HasFactory;
    use Publicable;
    use BelongsToSortedManyTrait;

    protected $casts = [
        'buildings_id' => 'array',
    ];

    /** @inheritdoc */
    protected $fillable = [
        'title',
        'description',
        'category',
        'count',
        'type',
        'destination',
        'uk_project_id',
        'is_published',
        'buildings_id',
        'preview_image_id',
        'tag',
        'url',
        'should_send_notification',
        'buildings_id',
        'is_sent'
    ];

    protected static function booted()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->published();
        });
    }

    public function getTypeAttribute(string $value): NewsType
    {
        return NewsType::from($value);
    }

    public function setTypeAttribute(NewsType $type): void
    {
        $this->attributes['type'] = $type->value;
    }

    public function getDestinationTypeAttribute(string $value): NotificationDestinationType
    {
        return NotificationDestinationType::from($value);
    }

    public function setDestinationTypeAttribute(NotificationDestinationType $type): void
    {
        $this->attributes['destination'] = $type->value;
    }

    public function getCategoryAttribute(string $value): NewsCategory
    {
        return NewsCategory::from($value);
    }

    public function setCategoryAttribute(NewsCategory $category): void
    {
        $this->attributes['category'] = $category->value;
    }

    public function contentItems(): BelongsToSortedMany
    {
        return $this->belongsToSortedMany(ContentItem::class, 'order', 'news_content_item');
    }

    public function scopeByType(Builder $query, NewsType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function scopeByCategory(Builder $query, ?NewsCategory $category): Builder
    {
        if ($category) {
            $query->where('category', $category->value);
        }

        return $query;
    }

    public function scopeByUkProjectId(Builder $query, ?int $ukProjectId): Builder
    {
        if ($ukProjectId) {
            $query->where('uk_project_id', $ukProjectId);
        }

        return $query;
    }

    /**
     * @param Builder   $query
     * @param Account[] $accounts
     *
     * @return Builder
     */
    public function scopeByAccounts(Builder $query, array $accounts): Builder
    {
        $ukProjectIds = [];
        foreach ($accounts as $account) {
            if ($account->getUkProject()) {
                $ukProjectIds[] = $account->getUkProject()->id;
            }
        }

        return $query->whereIn('uk_project_id', $ukProjectIds);
    }

    public function previewImage(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function ukProject(): BelongsTo
    {
        return $this->belongsTo(UkProject::class);
    }

    public function isCommon(): bool
    {
        return $this->type->isCommon();
    }
}
