<?php

namespace App\Models\Article;

use App\Components\Publication\Publicable;
use App\Components\Sorting\BelongsToSortedManyTrait;
use App\Models\ContentItem\ContentItem;
use App\Models\Image;
use App\Models\Project\Project;
use App\Models\UkProject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class Article
 *
 * @property int                $id
 * @property int                $project_id
 * @property boolean            $is_published
 * @property string             $title
 * @property int|null           $order
 * @property Carbon|null        $created_at
 * @property Carbon|null        $updated_at
 * @property int|null           $icon_image_id
 *
 * @property-read ContentItem[] $contentItems
 * @property-read Image         $iconImage
 *
 * @package App\Models\Article
 */
class Article extends Model
{
    use HasFactory;
    use Publicable;
    use BelongsToSortedManyTrait;
    use SortableTrait;

    /** @inheritdoc */
    protected $fillable = [
        'project_id',
        'is_published',
        'title',
        'order',
        'icon_image_id',
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

    public function scopeByProject(Builder $query, Project $project, $withoutScope = false): Builder
    {
        return $query->whereHasMorph(
            'articlable',
            Project::class,
            function (Builder $query) use ($project, $withoutScope) {
                if ($withoutScope) {
                    $query->withoutGlobalScope('published');
                }

                $query->where('id', $project->id);
            }
        );
    }

    public function scopeByUkProject(Builder $query, UkProject $ukProject, $withoutScope = false): Builder
    {
        return $query->whereHasMorph(
            'articlable',
            UkProject::class,
            function (Builder $query) use ($ukProject, $withoutScope) {
                if ($withoutScope) {
                    $query->withoutGlobalScope('published');
                }

                $query->where('id', $ukProject->id);
            }
        );
    }

    public function contentItems(): BelongsToMany
    {
        return $this->belongsToSortedMany(ContentItem::class, 'order', 'article_content_item');
    }

    public function articlable(): MorphTo
    {
        return $this->morphTo();
    }

    public function iconImage(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }
}
