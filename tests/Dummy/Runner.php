<?php


namespace Tests\Dummy;

use Illuminate\Support\Str;
use Sculptor\Foundation\Contracts\Response;
use Sculptor\Foundation\Runner\Response as Result;
use Sculptor\Foundation\Contracts\Runner as RunnerInterface;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Runner implements RunnerInterface
{
    private static array $commands = [];

    public function find(string $command): bool
    {
        foreach (static::$commands as $item) {
            if (Str::startsWith($item, $command)) {
                return true;
            }
        }

        return false;
    }

    public function tty(): RunnerInterface
    {
        return $this;
    }

    public function timeout(?int $timeout): RunnerInterface
    {
        return $this;
    }

    public function from(string $path): RunnerInterface
    {
        return $this;
    }

    public function input(string $input): RunnerInterface
    {
        return $this;
    }

    public function env(array $export): RunnerInterface
    {
        return $this;
    }

    public function run(array $command): Response
    {
        static::$commands[] = join(' ', $command);

        return new Result(true, '');
    }

    public function runOrFail(array $command): string
    {
        static::$commands[] = join(' ', $command);

        return 'Dummy runner';
    }

    public function realtime(array $command, callable $retrun): Response
    {
        return new Result(true, '');
    }

    public function line(): string
    {
        return 'Dummy runner';
    }
}
