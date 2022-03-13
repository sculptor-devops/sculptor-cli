<?php

namespace Sculptor\Agent\Logs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Sculptor\Agent\Enums\LogContextLevel;
use Sculptor\Agent\Logs\Contracts\LogContext;
use Throwable;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class LogsContext implements LogContext
{
    /**
     * @var array
     */
    private array $context;

    /**
     * @param array $context
     * @return array
     */
    private function merge(array $context = []): array
    {
        return array_merge($context, $this->context);
    }

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::emergency($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::alert($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::critical($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::warning($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::notice($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug(string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        if (app()->version() == 'unreleased' || env('SCULPTOR_DEBUG_LOG') === 'true') {
            Log::debug($message, $context);
        }
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log(int $level, string $message, array $context = array()): void
    {
        $context = $this->merge($context);

        Log::log($level, $message, $context);
    }

    /**
     * @param Throwable $e
     */
    public function report(Throwable $e): void
    {
        report($e);
    }
}
