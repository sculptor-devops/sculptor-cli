<?php

namespace Sculptor\Agent\Monitors\System;

use Illuminate\Support\Arr;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Monitors\Contracts\Monitor;
use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Memory implements Monitor
{
    /**
     * @var Runner
     */
    private Runner $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    public function values(Configuration $configuration): array
    {
        $result = $this->runner->runOrFail(['free']);

        $free = explode("\n", trim($result));
        $mem = explode(" ", Arr::get($free, '1', ''));
        $mem = array_filter($mem);
        $mem = array_merge($mem);

        return [
            "{$this->name()}.total" => ceil(Arr::get($mem, '1', 0) * 1024),
            "{$this->name()}.used" => ceil(Arr::get($mem, '2', 0) * 1024)
        ];
    }

    public function name(): string
    {
        return 'memory';
    }
}
