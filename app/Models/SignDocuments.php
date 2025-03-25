<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SignDocuments
 *
 * @property int            $id
 * @property string         $name
 * @property string         $code
 * @property string         $document_id
 *
 * @package App\Models
 */
class SignDocuments extends Model
{
    use HasFactory;
    use Publicable;

    public $timestamps = false;

    /** @inheritdoc */
    protected $fillable = [
        'name',
        'code',
        'document_id',
    ];
}
