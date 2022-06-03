<?php

namespace Sculptor\Agent\Actions\Daemons\Services;

use Sculptor\Agent\Actions\Contracts\Service;
use Sculptor\Agent\Enums\DaemonGroupType;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Nginx implements Service
{
    public function name(): string
    {
        return 'nginx';
    }

    public function package(): string
    {
        return 'nginx';
    }

    public function group(): string
    {
        return DaemonGroupType::WEB;
    }
}
