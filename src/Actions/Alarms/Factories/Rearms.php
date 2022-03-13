<?php

namespace Sculptor\Agent\Actions\Alarms\Factories;

use Exception;
use Sculptor\Agent\Actions\Alarms\Contracts\Rearm;
use Sculptor\Agent\Actions\Support\Factory;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Rearms extends Factory
{
    /**
     * @throws Exception
     */
    public function make(string $rearm): Rearm
    {
        return $this->find($rearm);
    }
}
