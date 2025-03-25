<?php

namespace App\Models\Document;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeletedDocument
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $document_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models\Document
 */
class DeletedDocument extends Model
{
    protected $fillable = ['document_id'];
}
