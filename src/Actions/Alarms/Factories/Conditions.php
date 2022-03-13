<?php

namespace Sculptor\Agent\Actions\Alarms\Factories;

use Exception;
use Sculptor\Agent\Actions\Alarms\Contracts\Condition;
use Sculptor\Agent\Actions\Support\Factory;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Conditions extends Factory
{
    /**
     * @throws Exception
     */
    public function make(string $condition): Condition
    {
        return $this->find($condition);
    }
}
