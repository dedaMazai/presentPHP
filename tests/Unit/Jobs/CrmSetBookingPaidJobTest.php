<?php

namespace Tests\Unit;

use App\Jobs\CrmSetBookingPaidJob;
use App\Mail\CrmFailSetBookingPaidMail;
use App\Models\Sales\Deal;
use App\Models\Sales\Demand\DemandBookingType;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\User\User;
use App\Services\Sales\Deal\DealService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;
use Throwable;

class CrmSetBookingPaidJobTest extends TestCase
{
    protected function setUp(): void
    {
        $this->fakeQueue = false;
        parent::setUp();
        Carbon::setTestNow('07-06-2022');
    }

//    public function testFailedCompleteClaimPayJob()
//    {
//        config()->set('payments_notification.failed_attempts_before_notify', 1);
//        $expectedEmail = config('payments_notification.email');
//        $expectedView = file_get_contents(base_path('tests/Unit/Jobs/fixtures/booking_paid_notification.html'));
//        $deal = $this->prepareDeal();
//        $renderedView = (new CrmFailSetBookingPaidMail($deal))->render();
//        Mail::fake();
//
//        app()->bind(DealService::class, fn() => $this->getMockedDealService());
//        $job = new CrmSetBookingPaidJob($deal);
//
//        try {
//            Queue::push($job);
//        } catch (Throwable) {
//            Mail::assertSent(
//                CrmFailSetBookingPaidMail::class,
//                fn(CrmFailSetBookingPaidMail $mail) => $mail->hasTo($expectedEmail)
//            );
//        }
//
//        self::assertEquals($expectedView, $renderedView);
//    }

    private function prepareDeal(): Deal
    {
        $user = User::factory()->create(['id' => 1000]);

        return Deal::factory()->create([
            'demand_id' => 'b8dda3a3-efdb-350d-8d41-9dd92a8b5957',
            'user_id' => $user->id,
            'demand_status' => DemandStatus::selt(),
            'demand_booking_type' => DemandBookingType::paidMortgage(),
            'initial_begin_date' => now(),
            'initial_end_date' => now(),
        ]);
    }

    private function getMockedDealService(): MockInterface
    {
        $mock = $this->mock(DealService::class);
        $mock->allows('setBookingPaid')->andThrow(new Exception('error'));

        return $mock;
    }
}



