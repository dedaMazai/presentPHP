<?php

namespace App\Services\LegalPerson\ExternalApi;

use Spatie\Enum\Enum;

/**
 * Class ExternalClient
 *
 * @package App\Services\LegalPerson\ExternalApi
 */
class ExternalApiRequest extends Enum
{
    public const SUGGESTION_DADATA_POST = 'http://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party';
}
