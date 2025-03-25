<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use App\Components\Sorting\BelongsToSortedMany;
use App\Components\Sorting\BelongsToSortedManyTrait;
use App\Models\Contact\Contact;
use App\Models\ContentItem\ContentItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class Settings
 *
 * @property int                $id
 * @property string|null        $main_office_title
 * @property string|null        $main_office_address
 * @property string|null        $main_office_phone
 * @property string|null        $main_office_email
 * @property string|null        $main_office_lat
 * @property string|null        $main_office_long
 * @property string|null        $phone
 * @property string|null        $offer_url
 * @property string|null        $consent_url
 * @property string|null        $build_android_url
 * @property string|null        $build_ios_url
 * @property string|null        $claim_root_category_crm_id
 * @property string|null        $claim_pass_car_crm_service_id
 * @property string|null        $claim_pass_human_crm_service_id
 * @property Carbon|null        $created_at
 * @property Carbon|null        $updated_at
 *
 * @property-read ContentItem[] $contentItems
 *
 * @package App\Models
 */
class Settings extends Model
{
    use HasFactory;
    use Publicable;
    use BelongsToSortedManyTrait;

    /** @inheritdoc */
    protected $table = 'settings';

    /** @inheritdoc */
    protected $fillable = [
        'main_office_title',
        'main_office_address',
        'main_office_phone',
        'main_office_email',
        'main_office_lat',
        'main_office_long',
        'phone',
        'offer_url',
        'consent_url',
        'confidant_url',
        'build_android_url',
        'build_ios_url',
        'claim_root_category_crm_id',
        'claim_pass_car_crm_service_id',
        'claim_pass_human_crm_service_id',
        'refill_account_acquiring',
        'claim_payment_acquiring'
    ];

    public function contentItems(): BelongsToSortedMany
    {
        return $this->belongsToSortedMany(ContentItem::class, 'order', 'settings_content_item');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }
}
