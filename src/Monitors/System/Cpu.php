<?php

namespace Sculptor\Agent\Monitors\System;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use Sculptor\Agent\Configuration;
use Sculptor\Agent\Monitors\Contracts\Monitor;

class Cpu implements Monitor
{
    public function values(Configuration $configuration): array
    {
        return [ "{$this->name()}.load" => sys_getloadavg()[0] ];
    }

    public function name(): string
    {
        return 'cpu';
    }
}
