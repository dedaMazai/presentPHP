<?php

namespace Tests\Unit;

use App\Jobs\CrmRefillAccountBalanceJob;
use App\Mail\CrmFailCompletingRefillAccountBalanceMail;
use App\Models\User\User;
use App\Services\Account\AccountService;
use App\Services\Account\Dto\RefillBalanceDto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;
use Throwable;

class CrmCompleteRefillAccountBalanceJobTest extends TestCase
{
    protected function setUp(): void
    {
        $this->fakeQueue = false;
        parent::setUp();
        Carbon::setTestNow('07-06-2022');
    }

    public function testFailedCompleteRefillAccountBalanceJob()
    {
        config()->set('payments_notification.failed_attempts_before_notify', 1);
        $expectedEmail = config('payments_notification.email');
        $expectedView =
            file_get_contents(base_path('tests/Unit/Jobs/fixtures/refill_account_balance_notification.html'));
        $dto = $this->prepareRefillBalanceDto();
        $renderedView = (new CrmFailCompletingRefillAccountBalanceMail($dto))->render();
        $paymentMethod = 'PSB';

        Mail::fake();
        app()->bind(AccountService::class, fn() => $this->getMockedAccountService());
        $job = new CrmRefillAccountBalanceJob($dto, $paymentMethod);

        try {
            Queue::push($job);
        } catch (Throwable $e) {
            Mail::assertSent(
                CrmFailCompletingRefillAccountBalanceMail::class,
                fn(CrmFailCompletingRefillAccountBalanceMail $mail) => $mail->hasTo($expectedEmail)
            );
        }

        self::assertEquals($expectedView, $renderedView);
    }

    private function prepareRefillBalanceDto(): RefillBalanceDto
    {
        return new RefillBalanceDto(
            'account_number',
            User::factory()->create(['id' => 1000]),
            1000,
            'payment_id',
            now(),
        );
    }

    private function getMockedAccountService(): MockInterface
    {
        $mock = $this->mock(AccountService::class);
        $mock->allows('refillBalance')->andThrow(new Exception('error'));

        return $mock;
    }
}



