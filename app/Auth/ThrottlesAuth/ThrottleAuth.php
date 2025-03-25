<?php

namespace App\Auth\ThrottlesAuth;

use App\Models\User\Ban;
use App\Models\User\BanHistory;
use App\Models\User\BanIp;
use App\Models\User\BanPhone;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Trait ThrottleAuth
 *
 * @package App\Auth\ThrottlesAuth
 */
trait ThrottleAuth
{
    public string $ip;
    public string $phone;
    public string $currentTime;
    public string $futereTime;

    protected function assertLimitOfAuthAttempts(Request $request)
    {
        $this->ip = $request->ip();
        $this->phone = str_replace("+", "", $request->get('phone'));
        $this->currentTime = Carbon::now()->toDateTimeString();
        $this->futereTime = Carbon::now()->addMinute()->toDateTimeString();
        $this->checkBan();
        $this->checkBanHistory();
        $this->checkBanByPhone();
        $this->checkBansByIp();
        $this->checkBansByPhone();
    }

    protected function checkBan()
    {
        $ban = BanIp::byIp($this->ip)->byActive()->get();

        if ($ban->count() == 0) {
            $ban = BanPhone::byPhone($this->phone)->byActive()->get();

            if ($ban->count() != 0) {
                return $this->sendLockoutResponse();
            }
        } else {
            return $this->sendLockoutResponse();
        }
    }

    protected function checkBanHistory()
    {
        $banHistory = BanHistory::byIp($this->ip)->byPhone($this->phone)->first();
        $banByIp = BanIp::byIp($this->ip)->get()->first();
        $banByPhone = BanPhone::byPhone($this->phone)->get()->first();

        if (!$banHistory) {
            BanHistory::create([
                'ip_address' => $this->ip,
                'phone_number' => $this->phone,
                'failed_attempts' => 1,
                'timestamp' => $this->currentTime
            ]);
        } else {
            if (Carbon::parse($banHistory->timestamp)->addMinute()->toDateTimeString() > $this->currentTime ||
                $banByIp || $banByPhone) {
                $banHistory->update([
                    'failed_attempts' => $banHistory->failed_attempts + 1,
                    'timestamp' => $this->currentTime
                ]);
            } else {
                $banHistory->update([
                    'failed_attempts' => 1,
                    'timestamp' => $this->currentTime
                ]);
            }
        }
    }

    protected function checkBanByPhone()
    {
        $banByPhone = BanHistory::byActive()->byPhone($this->phone)->byBanned()->first();

        if ($banByPhone) {
            $unlockTime = Carbon::parse($banByPhone->timestamp)
                ->addMinutes($this->maxAuthAttempts($banByPhone->failed_attempts))
                ->toDateTimeString();
            BanPhone::create([
                'phone_number' => '+' . $banByPhone->phone_number,
                'block_time' => $banByPhone->timestamp,
                'unlock_time' => $unlockTime,
                'attempts' => $banByPhone->failed_attempts,
            ]);

            if ($banByPhone->failed_attempts >= 5) {
                $this->sendMailByPhone(
                    '+' . $banByPhone->phone_number,
                    $banByPhone->timestamp,
                    $unlockTime,
                    $banByPhone->failed_attempts
                );
            }
        }
    }

    protected function checkBansByIp()
    {
        $bansByIp = BanHistory::byActive()->byIp($this->ip)->get();

        if ($bansByIp) {
            $failed_attempts = 0;

            foreach ($bansByIp as $banByIp) {
                $failed_attempts += $banByIp->failed_attempts;
            }

            if ($failed_attempts >= 3) {
                $unlockTime = Carbon::parse($this->currentTime)
                    ->addMinutes($this->maxAuthAttempts($failed_attempts))
                    ->toDateTimeString();

                BanIp::create([
                    'ip_address' => $this->ip,
                    'block_time' => $this->currentTime,
                    'unlock_time' => $unlockTime,
                    'attempts' => $failed_attempts, // отправляем запрос на блокировку соотв unlock_time
                ]);

                if ($failed_attempts >= 5) {
                    $phones = BanHistory::where('ip_address', $this->ip)->pluck('phone_number');

                    $this->sendMailByIp(
                        $this->ip,
                        $this->currentTime,
                        $unlockTime,
                        $failed_attempts,
                        $phones->toArray()
                    );
                }
            }
        }
    }

    protected function checkBansByPhone()
    {
        $bansByPhone = BanHistory::byActive()->byPhone($this->phone);
        if ($bansByPhone) {
            $failed_attempts = 0;

            foreach ($bansByPhone as $banByPhone) {
                $failed_attempts += $banByPhone->failed_attempts;
            }

            if ($failed_attempts >= 3) {
                $unlockTime = Carbon::parse($this->currentTime)
                    ->addMinutes($this->maxAuthAttempts($failed_attempts))
                    ->toDateTimeString();

                BanIp::create([
                    'ip_address' => $this->ip,
                    'block_time' => $this->currentTime,
                    'unlock_time' => $unlockTime,
                    'attempts' => $failed_attempts, // отправляем запрос на блокировку соотв unlock_time
                ]);

                if ($failed_attempts >= 5) {
                    $phones = BanHistory::where('ip_address', $this->ip)->pluck('phone_number');

                    $this->sendMailByIp(
                        $this->ip,
                        $this->currentTime,
                        $unlockTime,
                        $failed_attempts,
                        $phones->toArray()
                    );
                }
            }
        }
    }

    protected function clearAuthAttempts(string $ip, string $phone)
    {
        $phone = str_replace("+", "", $phone);

        BanIp::where('ip_address', $ip)->delete();
        BanPhone::where('phone_number', $phone)->delete();
        BanHistory::where('ip_address', $ip)->delete();
        BanHistory::where('phone_number', $phone)->delete();
    }

    protected function sendLockoutResponse()
    {
        throw ValidationException::withMessages([
            'message' => ['Too many attempts.'],
        ])->status(429);
    }

    protected function maxAuthAttempts($number)
    {
        if ($number >= 5) {
            $number = 5;
        }

        $numberOfAttempts = [
            3 => 15,
            4 => 60,
            5 => 1440
        ];

        return $numberOfAttempts[$number]??null;
    }

    private function sendMailByPhone(
        string $phoneNumber,
        string $blockTime,
        string $unblockTime,
        string $attemps,
    ) {
        $html = "<p>Номер мобильного телефона: $phoneNumber</p>".
            "<p>Время блокировки: $blockTime MSK (UTC+3)</p>".
            "<p>Время разблокировки: $unblockTime MSK (UTC+3)</p>".
            "<p>Количество неуспешных попыток авторизации: $attemps</p>".
            "<p>Замечена подозрительная активность. Пользователь заблокирован на 24 часа.</p>";

        Mail::html(
            $html,
            fn($mail) => $mail->to(['malov121998@list.ru', 'maksim.pavlov@ramax.ru'])
                ->subject('Замечена подозрительная активность')
        );

        $this->getLoggerPhone()->info(json_encode([
            'content' => $html,
        ]));
    }

    private function sendMailByIp(
        string $ipAddress,
        string $blockTime,
        string $unblockTime,
        string $attemps,
        array $phoneNumbers,
    ) {
        $phones = "";

        foreach ($phoneNumbers as $phoneNumber) {
            $phones .= "<p>$phoneNumber<p>";
        }

        $html = "<p>IP-адрес: $ipAddress</p>".
            "<p>Время блокировки: $blockTime MSK (UTC+3)</p>".
            "<p>Время разблокировки: $unblockTime MSK (UTC+3)</p>".
            "<p>Количество неуспешных попыток авторизации: $attemps</p>".
            "<p>Номера телефонов, по которым совершались неуспешные попытки авторизации:</p>".
            $phones.
            "<p>Замечена подозрительная активность. Пользователь заблокирован на 24 часа.</p>";

        Mail::html(
            $html,
            fn($mail) => $mail->to(['malov121998@list.ru', 'maksim.pavlov@ramax.ru'])
                ->subject('Замечена подозрительная активность')
        );

        $this->getLoggerIp()->info(json_encode([
            'content' => $html,
        ]));
    }

    private function getLoggerPhone()
    {
        $dateString = now()->format('m_d_Y');
        $filePath = 'blocked_users_by_phone' . $dateString . '.log';
        $dateFormat = "m/d/Y H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler(storage_path('logs/' . $filePath), Logger::DEBUG);
        $stream->setFormatter($formatter);
        $processId = Str::random(5);
        $logger = new Logger($processId);
        $logger->pushHandler($stream);

        return $logger;
    }

    private function getLoggerIp()
    {
        $dateString = now()->format('m_d_Y');
        $filePath = 'blocked_users_by_ip' . $dateString . '.log';
        $dateFormat = "m/d/Y H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler(storage_path('logs/' . $filePath), Logger::DEBUG);
        $stream->setFormatter($formatter);
        $processId = Str::random(5);
        $logger = new Logger($processId);
        $logger->pushHandler($stream);

        return $logger;
    }
}
