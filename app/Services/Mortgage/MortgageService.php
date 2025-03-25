<?php

namespace App\Services\Mortgage;

use App\Services\Mortgage\Dto\GetLoanOffersRequestDto;
use Illuminate\Support\Collection;

/**
 * Class MortgageService
 *
 * @package App\Services\Mortgage
 */
class MortgageService
{
    public function __construct(private MortgageClient $client)
    {
    }

    public function getLoanOfferList(GetLoanOffersRequestDto $dto): Collection
    {
        return collect($this->client->getLoanOffers($dto));
    }
}
