<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

/**
 * Class RemoveOldLogs
 *
 * @package App\Console\Commands
 */
class RemoveOldLogs extends Command
{
    protected $signature = 'old-logs:remove';


    public function handle(): void
    {
        $logPath = storage_path('logs');
        $files = File::allFiles($logPath);

        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $filePath = $file->getRealPath();
            $fileExtension = $file->getExtension();

            if ($fileExtension === 'log' && preg_match('/^crm.*requests_\d{2}_\d{2}_\d{4}\.log$/', $fileName)) {
                $fileDate = Carbon::createFromFormat('d_m_Y', substr($fileName, -14, 10));
                $diffInDays = Carbon::now()->diffInDays($fileDate);

                if ($diffInDays > 7) {
                    File::delete($filePath);
                }
            }

            if ($fileExtension === 'log' && preg_match('/^web_hooks_\d{2}_\d{2}_\d{4}\.log$/', $fileName)) {
                $fileDate = Carbon::createFromFormat('m_d_Y', substr($fileName, -14, 10));
                $diffInDays = Carbon::now()->diffInDays($fileDate);

                if ($diffInDays > 7) {
                    File::delete($filePath);
                }
            }

            if ($fileExtension === 'log' && preg_match('/^blocked_users_\d{2}_\d{2}_\d{4}\.log$/', $fileName)) {
                $fileDate = Carbon::createFromFormat('m_d_Y', substr($fileName, -14, 10));
                $diffInDays = Carbon::now()->diffInDays($fileDate);

                if ($diffInDays > 7) {
                    File::delete($filePath);
                }
            }

            if ($fileExtension === 'log' && preg_match('/^psb_payments_\d{2}_\d{2}_\d{4}\.log$/', $fileName)) {
                $fileDate = Carbon::createFromFormat('d_m_Y', substr($fileName, -14, 10));
                $diffInDays = Carbon::now()->diffInDays($fileDate);

                if ($diffInDays > 7) {
                    File::delete($filePath);
                }
            }

            if ($fileExtension === 'log' && preg_match('/^sbp_payments_\d{2}_\d{2}_\d{4}\.log$/', $fileName)) {
                $fileDate = Carbon::createFromFormat('d_m_Y', substr($fileName, -14, 10));
                $diffInDays = Carbon::now()->diffInDays($fileDate);

                if ($diffInDays > 7) {
                    File::delete($filePath);
                }
            }
        }

        $this->info('Old logs removed.');
    }
}
