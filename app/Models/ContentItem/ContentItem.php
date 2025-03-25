<?php

namespace App\Models\ContentItem;

use App\Models\Document;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class ContentItem
 *
 * @property int             $id
 * @property ContentItemType $type
 * @property string|null     $text
 * @property int|null        $image_id
 * @property string|null     $video_url
 * @property int|null        $order
 * @property Carbon|null     $created_at
 * @property Carbon|null     $updated_at
 * @property array           $content
 *
 * @property-read Image|null $image
 * @property-read Image[]    $images
 *
 * @package App\Models\ContentItem
 */
class ContentItem extends Model
{
    use HasFactory;

    /** @inheritdoc */
    protected $fillable = [
        'type',
        'text',
        'image_id',
        'video_url',
        'content',
        'document_id'
    ];

    /** @inheritdoc */
    protected $casts = [
        'content' => AsArrayObject::class,
    ];

    public function getTypeAttribute(string $value): ContentItemType
    {
        return ContentItemType::from($value);
    }

    public function setTypeAttribute(ContentItemType $type): void
    {
        $this->attributes['type'] = $type->value;
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'content_item_image');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'content_item_document');
    }
}
