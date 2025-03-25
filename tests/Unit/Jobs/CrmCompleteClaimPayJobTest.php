<?php

namespace Tests\Unit;

use App\Jobs\CrmCompleteClaimPayJob;
use App\Mail\CrmFailCompletingClaimPayMail;
use App\Models\Claim\Claim;
use App\Models\Claim\ClaimStatus;
use App\Models\Claim\ClaimTheme;
use App\Models\User\User;
use App\Services\Claim\ClaimService;
use App\Services\Claim\Dto\SetClaimPaidDto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;
use Throwable;

class CrmCompleteClaimPayJobTest extends TestCase
{
    protected function setUp(): void
    {
        $this->fakeQueue = false;
        parent::setUp();
        Carbon::setTestNow('07-06-2022');
    }

    public function testFailedCompleteClaimPayJob()
    {
        config()->set('payments_notification.failed_attempts_before_notify', 1);
        $expectedEmail = config('payments_notification.email');
        $expectedView = file_get_contents(base_path('tests/Unit/Jobs/fixtures/claim_pay_notification.html'));
        $dto = $this->prepareClaimDto();
        $renderedView = (new CrmFailCompletingClaimPayMail($dto))->render();
        $paymentMethod = 'PSB';
        Mail::fake();

        app()->bind(ClaimService::class, fn() => $this->getMockedClaimService());
        $job = new CrmCompleteClaimPayJob($dto, $paymentMethod);

        try {
            Queue::push($job);
        } catch (Throwable $e) {
            Mail::assertSent(
                CrmFailCompletingClaimPayMail::class,
                fn(CrmFailCompletingClaimPayMail $mail) => $mail->hasTo($expectedEmail)
            );
        }

        self::assertEquals($expectedView, $renderedView);
    }

    private function prepareClaimDto(): SetClaimPaidDto
    {
        $claim = new Claim(
            id: '1',
            number: null,
            theme: ClaimTheme::request(),
            status: ClaimStatus::new(),
            createdAt: now(),
            closedAt: null,
            paymentStatus: null,
            comment: null,
            arrivalDate: null,
            paymentDate: null,
            totalPayment: null,
            scheduledStart: null,
            scheduledEnd: null,
            user: User::factory()->create([
                'id' => 1000,
                'crm_id' => 'crm_id123',
            ]),
            executors: [],
            passType: null,
            passCar: null,
            passHuman: null,
            passStatus: null,
            services: [],
            images: [],
            confirmationCode: null,
            commentQuality: null,
            rating: null,
            vendorId: null,
            vendorName: null,
            accountNumber: null
        );

        return new SetClaimPaidDto($claim, 'paymen_id', now());
    }

    private function getMockedClaimService(): MockInterface
    {
        $mock = $this->mock(ClaimService::class);
        $mock->allows('setPaid')->andThrow(new Exception('error'));

        return $mock;
    }
}



