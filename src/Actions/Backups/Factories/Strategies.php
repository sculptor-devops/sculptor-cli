<?php

namespace Sculptor\Agent\Actions\Backups\Factories;

use Exception;
use Sculptor\Agent\Actions\Backups\Contracts\Strategy;
use Sculptor\Agent\Actions\Support\Factory;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Strategies extends Factory
{
    /**
     * @throws Exception
     */
    public function make(string $strategy): Strategy
    {
        return $this->find($strategy);
    }
}
