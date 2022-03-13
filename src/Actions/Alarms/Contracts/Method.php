<?php

namespace Sculptor\Agent\Actions\Alarms\Contracts;

use Sculptor\Agent\Actions\Alarms\Support\Context;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Method
{
    public function name(): string;
    public function exec(Context $context): int;
    function parameters(Parameters $parameters): Method;
}
