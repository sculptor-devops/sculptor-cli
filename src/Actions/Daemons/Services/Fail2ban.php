<?php

namespace Sculptor\Agent\Actions\Daemons\Services;

use Sculptor\Agent\Actions\Contracts\Service;
use Sculptor\Agent\Enums\DaemonGroupType;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Fail2ban implements Service
{
    function name(): string
    {
        return 'fail2ban';
    }

    function package(): string
    {
        return 'fail2ban';
    }

    function group(): string
    {
        return DaemonGroupType::REMOTE;
    }
}
