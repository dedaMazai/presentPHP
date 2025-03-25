<?php

namespace App\Jobs;

 use App\Models\Account\AccountNumbers;
use App\Services\Contract\ContractRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAccountNumberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 6000;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private string $user_id,
        private string $crm_id,
    ) {
    }

    public function handle(ContractRepository $repository)
    {
        try {
            $accounts = $repository->getUserAccountNumbersAndRoles($this->crm_id);

            $currentAccountNumbers = AccountNumbers::where('user_id', '=', $this->user_id)
                ->pluck('account_number')
                ->toArray();
            $accountNumbersFromCrm = array_column($accounts, 'accountNumber');
            $differences = array_diff($currentAccountNumbers, $accountNumbersFromCrm);

            foreach ($accounts as $account) {
                AccountNumbers::updateOrCreate(
                    [
                        'user_id' => $this->user_id,
                        'account_number' => $account['accountNumber'],
                    ],
                    [
                        'role' => $account['role'],
                    ]
                );
            }

            AccountNumbers::where('user_id', '=', $this->user_id)->whereIn('account_number', $differences)->delete();
        } catch (Exception $exception) {
            logger()->debug("UpdateAccount $this->crm_id with error " . $exception->getMessage());
        }
    }
}
