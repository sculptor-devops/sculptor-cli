<?php

namespace Sculptor\Agent\Actions\Daemons\Services;

use Sculptor\Agent\Actions\Contracts\Service;
use Sculptor\Agent\Enums\DaemonGroupType;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Redis implements Service
{
    function name(): string
    {
        return 'redis';
    }

    function package(): string
    {
        return 'redis-server';
    }

    function group(): string
    {
        return DaemonGroupType::QUEUE;
    }
}
