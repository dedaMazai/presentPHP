<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DocumentsName
 *
 * @property int            $id
 * @property string         $code
 * @property string         $name
 * @property string|null    $description
 * @property int            $object_type_code
 *
 * @package App\Models
 */
class DocumentsName extends Model
{
    use HasFactory;
    use Publicable;

    protected $table = 'documents_name';

    public $timestamps = false;

    /** @inheritdoc */
    protected $fillable = [
        'code',
        'name',
        'description',
        'object_type_code',
    ];
}
