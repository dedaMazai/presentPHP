<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Illuminate\Support\Str;

class ApiRequestLogging
{
    private $logger;

    public function __construct()
    {
        $this->logger = $this->getLogger();
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $url = $request->url();
        $queryString = $request->getQueryString();
        $method = $request->method();
        $ip = $request->ip();
        $headers = $this->getHeadersFromRequest();
        $body = $request->getContent();
        $methodUrlString = "$ip $method $url";
        if ($queryString) {
            $methodUrlString .= "?$queryString";
        }

        if (array_key_exists('Authorization', $headers)) {
            $headers['Authorization'] = 'xxxxxxx';
        }

        $this->logger->info(json_encode([
            'method' => $methodUrlString,
            'headers' => $this->escapeSequenceDecode(json_encode($headers)),
            'body' => $this->escapeSequenceDecode($body),
            'response' => $this->escapeSequenceDecode($response),
        ]));
    }

    private function getLogger()
    {
        $dateString = now()->format('m_d_Y');
        $filePath = 'web_hooks_' . $dateString . '.log';
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

    private function getHeadersFromRequest()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

    private function escapeSequenceDecode($str)
    {
        $regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})|\\\u([\da-fA-F]{4})/sx';

        return preg_replace_callback($regex, function ($matches) {
            if (isset($matches[3])) {
                $cp = hexdec($matches[3]);
            } else {
                $lead = hexdec($matches[1]);
                $trail = hexdec($matches[2]);

                $cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
            }

            if ($cp > 0xD7FF && 0xE000 > $cp) {
                $cp = 0xFFFD;
            }

            if ($cp < 0x80) {
                return chr($cp);
            } elseif ($cp < 0xA0) {
                return chr(0xC0 | $cp >> 6).chr(0x80 | $cp & 0x3F);
            }

            return html_entity_decode('&#'.$cp.';');
        }, $str);
    }
}
