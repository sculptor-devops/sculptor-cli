<?php

namespace Sculptor\Agent\Support;

use Illuminate\Support\Carbon;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Chronometer
{
    private static bool $mock = false;
    private static Carbon $mocked;
    private static int $milliseconds = 0;

    /**
     * @var Carbon
     */
    private Carbon $started;

    /**
     * Chronometer constructor.
     */
    public function __construct()
    {
        $this->started = now();
    }

    /**
     * @return Chronometer
     */
    public static function start(): Chronometer
    {
        return new Chronometer();
    }

    /**
     * @return string
     */
    public function stop(): string
    {
        return now()->longAbsoluteDiffForHumans($this->started);
    }

    public function elapsed(): int
    {
        if (static::$mock) {
            return static::$milliseconds;
        }

        return now()->diffInMilliseconds($this->started);
    }

    public static function now(): Carbon
    {
        if (static::$mock) {
            return static::$mocked;
        }

        return now();
    }

    public static function tag(): string
    {
        $now = static::now();

        return $now->format("Ymd-His");
    }

    public static function mock(string $date, int $milliseconds): void
    {
        static::$mock = true;

        static::$mocked = Carbon::parse($date);

        static::$milliseconds = $milliseconds;
    }
}
