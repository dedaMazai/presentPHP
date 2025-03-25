<?php

namespace App\Jobs;

use App\Services\Sberbank\SberbankClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class RetryReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $errorStatuses = [
        'Чек не найден',
        'Чек не может быть отправлен',
        'Чек уже изменен ранее'
    ];

    private array $waitingStatuses = [
        'Ожидается статус регистрации в кассовом сервисе'
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private array $retryIds,
        private string $messageBody
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        SberbankClient $client,
    ) {
        $response = $client->retryReceipt($this->retryIds);

        $message = $this->messageBody . json_encode($response);

        if (in_array($response['result'], $this->errorStatuses)) {
            Http::post("https://" . $_SERVER["APP_URL"] . "/api/v1/feedback-appeal", [
                'message' => $message
            ]);
        } elseif (in_array($response['result'], $this->waitingStatuses)) {
            RetryReceiptJob::dispatch($this->retryIds, $this->messageBody)
                ->onQueue('default')
                ->delay(now()->addHours(15));
        };
    }
}
