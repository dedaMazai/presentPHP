<?php

namespace Tests\Feature\Api\External;

use App\Jobs\CrmCompleteClaimPayJob;
use App\Jobs\CrmRefillAccountBalanceJob;
use App\Models\Claim\Claim;
use App\Models\Claim\ClaimStatus;
use App\Models\Claim\ClaimTheme;
use App\Models\TransactionLog\SberbankOperationType;
use App\Models\TransactionLog\TransactionLog;
use App\Models\User\User;
use App\Services\Claim\ClaimRepository;
use App\Services\Claim\ClaimService;
use App\Services\Crm\CrmClient;
use App\Services\Sberbank\ChecksumValidator;
use App\Services\Sberbank\SberbankClient;
use Exception;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
//    private User $user;
//
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        $this->user = User::factory(1)->state(['phone' => '+79999999999'])->create()->first();
//        $token = $this->user->createToken('auth_token')->plainTextToken;
//        $this->withHeader('Authorization', "Bearer {$token}");
//    }
//
//    public function callbackData(): array
//    {
//        return [
//            [
//                'orderNumber' => 'transaction-1',
//                'claimId' => '1',
//                'job' => CrmCompleteClaimPayJob::class,
//                'queue' => 'claim_payments',
//            ],
//            [
//                'orderNumber' => 'transaction-1',
//                'claimId' => null,
//                'job' => CrmRefillAccountBalanceJob::class,
//                'queue' => 'refill_account_payments',
//            ],
//        ];
//    }
//
//    /**
//     * @dataProvider callbackData
//     * @return void
//     */
//    public function testCallback(string $orderNumber, ?string $claimId, string $job, string $queue)
//    {
//        app()->bind(ClaimService::class, fn() => $this->getMockedClaimService());
//        app()->bind(CrmClient::class, fn() => $this->getMockedCrmClient());
//        app()->bind(SberbankClient::class, fn() => $this->getMockedSberbankClient());
//        app()->bind(ChecksumValidator::class, fn() => $this->getMockedChecksumValidator());
//        app()->bind(ClaimRepository::class, fn() => $this->getMockedClaimRepository());
//        TransactionLog::factory()->create(['id' => 1, 'claim_id' => $claimId]);
//
//        $data = [
//            'amount' => 111,
//            'mdOrder' => '12341234',
//            'orderNumber' => $orderNumber,
//            'checksum' => 'sdfwef',
//            'operation' => SberbankOperationType::deposited(),
//            'status' => 1,
//            'payment_method' => 'PSB'
//        ];
//
//        $response = $this->json('get', 'api/v1/payments/callback', $data);
//        $response->assertNoContent();
//        Queue::assertPushedOn($queue, $job);
//    }
//
//    private function getMockedClaimService(): MockInterface
//    {
//        $mock = $this->mock(ClaimService::class);
//        $mock->allows('setPaid')->andThrow(new Exception('error'));
//
//        return $mock;
//    }
//
//    private function getMockedCrmClient(): MockInterface
//    {
//        return $this->mock(CrmClient::class);
//    }
//
//    private function getMockedSberbankClient(): MockInterface
//    {
//        return $this->mock(SberbankClient::class);
//    }
//
//    private function getMockedChecksumValidator(): MockInterface
//    {
//        $mock = $this->mock(ChecksumValidator::class);
//        $mock->allows('validate')->andReturnTrue();
//
//        return $mock;
//    }
//
//    private function getMockedClaimRepository(): MockInterface
//    {
//        $mock = $this->mock(ClaimRepository::class);
//        $mock->allows('getOneById')->andReturn($this->makeClaim());
//
//        return $mock;
//    }
//
//    private function makeClaim(): Claim
//    {
//        return new Claim(
//            id: '1',
//            number: null,
//            theme: ClaimTheme::request(),
//            status: ClaimStatus::new(),
//            createdAt: now(),
//            closedAt: null,
//            paymentStatus: null,
//            comment: null,
//            arrivalDate: null,
//            paymentDate: null,
//            totalPayment: null,
//            scheduledStart: null,
//            scheduledEnd: null,
//            user: User::factory()->create(),
//            executors: [],
//            passType: null,
//            passCar: null,
//            passHuman: null,
//            passStatus: null,
//            services: [],
//            images: [],
//            confirmationCode: null,
//            commentQuality: null,
//            rating: null,
//            vendorId: null,
//            vendorName: null,
//        );
//    }
}
