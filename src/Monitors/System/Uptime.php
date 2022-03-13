<?php

namespace Sculptor\Agent\Monitors\System;

use Sculptor\Agent\Configuration;
use Sculptor\Agent\Monitors\Contracts\Monitor;

class Uptime implements Monitor
{
    public function values(Configuration $configuration): array
    {
        $uptime = file_get_contents("/proc/uptime");

        return ["{$this->name()}.ticks" => explode(" ", $uptime)[0]];
    }

    public function name(): string
    {
        return 'uptime';
    }
}
