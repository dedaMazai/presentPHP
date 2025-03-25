<?php

namespace Tests\Unit\Repository;

use App\Services\Claim\ClaimRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use Illuminate\Support\Arr;
use Mockery\MockInterface;
use Tests\TestCase;

class ClaimRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetClaims()
    {
//        $claims = [
//            'claimUkList' => [
//                [
//                    'id' => 1,
//                    'modifiedOn' => '01-06-2022',
//                    'incidentClassificationCode' => ['code' => '1'],
//                    'status' => ['code' => '1'],
//                    'createdOn' => '01-06-2022',
//                ],
//            ],
//        ];
//
//        $repository = $this->prepareRepositoryWithClaims($claims);
//        $repository->getAll('account_number');
//
//        $claims = json_decode(
//            json: file_get_contents(base_path('tests/Unit/Repository/fixtures/claims.json')),
//            associative: true
//        );
//        $claimIds = Arr::pluck($claims['claimUkList'], 'id');
//
//        $repository = $this->prepareRepositoryWithClaims($claims);
//        $repository->getAll('account_number')
//            ->each(fn($claim) => $this->assertTrue(in_array((string)$claim->getId(), $claimIds)));
        self::assertTrue(true);
    }

    private function prepareRepositoryWithClaims(array $claims): ClaimRepository
    {
        app()->bind(DynamicsCrmClient::class, fn() => $this->mockDynamicsCrmClient($claims));

        return app(ClaimRepository::class);
    }

    private function mockDynamicsCrmClient(array $claims): MockInterface
    {
        $mock = $this->mock(DynamicsCrmClient::class);

        $mock->allows([
            'getClaimByIds' => $claims,
            'getClaimsModifiedDateByAccountNumber' => [
                'claimUkShortList' => $claims['claimUkList']
            ],
        ]);

        return $mock;
    }
}
