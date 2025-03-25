<?php

namespace App\Models\Project;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class ProjectType
 *
 * @property int            $id
 * @property string         $name
 * @property int|null       $order
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @property-read Project[] $projects
 *
 * @package App\Models\Project
 */
class ProjectType extends Model
{
    use HasFactory;
    use SortableTrait;

    /** @inheritdoc */
    protected $fillable = [
        'name',
        'order',
    ];

    protected static function booted()
    {
        static::addGlobalScope('ordered', function (Builder $builder) {
            $builder->ordered();
        });
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'type_id', 'id');
    }
}
