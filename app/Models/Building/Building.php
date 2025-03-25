<?php

namespace App\Models\Building;

use App\Components\Sorting\BelongsToSortedManyTrait;
use App\Models\UkProject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class Building
 *
 * @property int                $id
 * @property string             $build_name
 * @property string             $build_zid
 * @property int|null           $order
 * @property Carbon|null        $created_at
 * @property Carbon|null        $updated_at
 * @property string|null        $instruction_url
 *
 * @package App\Models\Building
 */
class Building extends Model
{
    use BelongsToSortedManyTrait;
    use SortableTrait;

    /** @inheritdoc */
    protected $fillable = [
        'id',
        'build_name',
        'build_zid',
        'instruction_url',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', static function (Builder $builder) {
            $builder->ordered();
        });
    }

    public function scopeByBuildZid(Builder $query, string $build_zid): Builder
    {
        return $query->where('build_zid', $build_zid);
    }

    public function scopeByUkProject(Builder $query, UkProject $ukProject): Builder
    {
        return $query->whereHasMorph(
            'project',
            UkProject::class,
            function (Builder $query) use ($ukProject) {
                $query->where('id', $ukProject->id);
            }
        );
    }

    public function project(): MorphTo
    {
        return $this->morphTo();
    }
}
