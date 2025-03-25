<?php

namespace App\Console\Commands;

use App\Jobs\ReloadAccountPopularTreeCache;
use App\Jobs\ReloadAccountTreeCache;
use App\Mail\CrmFailedMail;
use App\Mail\OutOfDiskSpace;
use App\Models\Account\AccountInfo;
use App\Services\Claim\ClaimCatalogueService;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class CheckingUsedMemory
 *
 * @package App\Console\Commands
 */
class CheckingUsedMemory extends Command
{
    protected $signature = 'disk-usage:check';
    private $email = "andrey.malov@ramax.ru";

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function handle(): void
    {
        $output = shell_exec('df -h');
        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            if (strpos($line, 'overlay') !== false) {
                $data = preg_split('/\s+/', $line);
                $totalSize = $data[1];
                $usedSize = $data[2];
                $availableSize = $data[3];
                $usagePercentage = $data[4];

                if ((int)$availableSize < 10) {
                    Mail::to($this->email)->send(new OutOfDiskSpace($availableSize));
                };
            }
        }
    }
}
