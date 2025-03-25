<?php

namespace App\Services\Mortgage;

use App\Services\Mortgage\Dto\GetLoanOffersRequestDto;
use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\RawObject;
use GraphQL\Results;
use RuntimeException;

/**
 * Class MortgageClient
 *
 * @package App\Services\Mortgage
 */
class MortgageClient
{
    private Client $client;

    public function __construct(private string $baseUrl)
    {
        $this->client = new Client($this->baseUrl);
    }

    public function getLoanOffers(GetLoanOffersRequestDto $dto): array
    {
        $query = $this->prepareLoanOffersQuery($dto);

        return $this->query($query)->getData()->getLoanOffer ?? [];
    }

    public function getAuthToken()
    {
        $query = $this->prepareAuthTokenQuery();

        return $this->query($query)->getData() ?? null;
    }

    public function getClientToken($phone, $uuid, $token)
    {
        $query = $this->prepareClientTokenQuery($phone, $uuid);

        return $this->queryWithToken($query, $token)->getData() ?? null;
    }

    private function query(Query $query): Results
    {
        try {
            return $this->client->runQuery($query);
        } catch (QueryError $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function queryWithToken(Query $query, $token): Results
    {
        $this->client = new Client($this->baseUrl, ['Authorization' => 'Bearer ' . $token]);

        try {
            return $this->client->runQuery($query);
        } catch (QueryError $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function prepareLoanOffersQuery(GetLoanOffersRequestDto $dto): Query
    {
        $arguments = [
            'age' => $dto->age,
            'cost' => $dto->cost,
            'housingComplexId' => $dto->housingComplexId,
            'initialPayment' => $dto->initialPayment,
            'isInsured' => $dto->isInsured,
            'isRfCitizen' => $dto->isRfCitizen,
            'loanPeriod' => $dto->loanPeriod,
            'lastJobExp' => $dto->lastJobExp,
            'overallExp' => $dto->overallExp,
            'agendaType' => new RawObject($dto->agendaType),
            'mortgageType' => new RawObject($dto->mortgageType),
            'proofOfIncome' => new RawObject($dto->proofOfIncome),
            'employmentType' => new RawObject($dto->employmentType),
        ];
        if ($dto->payrollProgramBankId) {
            $arguments['payrollProgramBankId'] = $dto->payrollProgramBankId;
        }

        return (new Query('getLoanOffer'))
            ->setArguments($arguments)
            ->setSelectionSet(
                [
                    'bankId',
                    'bankLogo',
                    'bankName',
                    'id',
                    'maxAge',
                    'maxCreditAmount',
                    'maxCreditPeriod',
                    'minAge',
                    'minInitialPayment',
                    'minLastJobExp',
                    'minOverallExp',
                    'name',
                    (new Query('periodParams'))->setSelectionSet(['monthlyPayment', 'months', 'rate']),
                    (new Query('periods'))->setSelectionSet(['amount', 'period']),
                    'rate',
                    'realtyCostIncreasePercent',
                    'recommendedIncomeCoeff',
                    'strictlyMatchesLoanPeriod',
                ]
            );
    }

    private function prepareAuthTokenQuery(): Query
    {
        $email = config('services.mortgage.manager_email');
        $password = config('services.mortgage.manager_password');

        return (new Mutation('loanOfficer_SignIn'))
            ->setArguments(
                ['input' => new RawObject('{email: "onlinepioneer@ipoteka.digital", password: "3FQKKlz1F8"}')]
            );
    }

    private function prepareClientTokenQuery($phone, $uuid): Query
    {
        $arguments = [
            'applicationUuid' => $uuid,
            'clientPhone' => $phone,
        ];

        return (new Query('getClientTokenWithoutPhoneVerification'))->setArguments($arguments);
    }
}
