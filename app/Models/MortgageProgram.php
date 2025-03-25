<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use App\Components\Sorting\BelongsToSortedManyTrait;
use App\Models\Project\Project;
use App\Models\Sales\Bank\BankInfo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class MortgageProgram
 *
 * @property int           $id
 * @property int           $project_id
 * @property int           $bank_info_id
 * @property float         $initial_payment
 * @property string        $citizenship
 * @property int           $period
 * @property string|null   $addresses
 * @property int|null      $order
 * @property Carbon|null   $created_at
 * @property Carbon|null   $updated_at
 *
 * @property-read Project  $project
 * @property-read BankInfo $bankInfo
 *
 * @package App\Models
 */
class MortgageProgram extends Model
{
    use Publicable;
    use BelongsToSortedManyTrait;
    use SortableTrait;

    /** @inheritdoc */
    protected $fillable = [
        'project_id',
        'bank_info_id',
        'initial_payment',
        'citizenship',
        'period',
        'addresses',
        'order',
    ];

    protected $casts = [
        'initial_payment' => 'float',
    ];

    protected static function booted()
    {
        static::addGlobalScope('ordered', function (Builder $builder) {
            $builder->ordered();
        });
    }

    public function scopeByProject(Builder $query, Project $project): Builder
    {
        return $query->where('project_id', $project->id);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function bankInfo(): BelongsTo
    {
        return $this->belongsTo(BankInfo::class);
    }
}
