<?php

namespace Sculptor\Agent\Actions\Alarms\Rearms;

use Sculptor\Agent\Actions\Alarms\Contracts\Rearm;
use Sculptor\Agent\Actions\Alarms\Support\Validable;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Auto extends Validable implements Rearm
{
    public array $properties = [ ];

    public function name(): string
    {
        return 'auto';
    }

    public function act(bool $current, bool $last): bool
    {
        return $current;
    }

    function parameters(Parameters $parameters): Rearm
    {
        parent::parameters($parameters);

        return $this;
    }
}
