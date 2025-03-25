<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class EscrowBanks
 *
 * @property int            $id
 * @property int            $escrow_bank_id
 * @property array          $letterofbank_ids
 *
 * @package App\Models
 */
class EscrowBanks extends Model
{
    use HasFactory;
    use Publicable;

    protected $casts = [
        'letterofbank_ids' => 'array',
    ];

    /** @inheritdoc */
    protected $fillable = [
        'escrow_bank_id',
        'letterofbank_ids',
    ];

    public $timestamps = false;

    public function bank(): HasOne
    {
        return $this->hasOne(Banks::class, 'id', 'escrow_bank_id');
    }
}
