<?php


namespace Sculptor\Agent\Actions\Alarms\Support;

use InvalidArgumentException;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Validable
{
    protected Parameters $parameters;

    public array $properties = [];

    function parameters(Parameters $parameters)
    {
        if (!sameKeys($this->properties, $parameters->keys())) {
            $diff = join(', ', array_diff($this->properties, $parameters->keys()));

            throw new InvalidArgumentException("Missing parameters: $diff");
        }

        $this->parameters = $parameters;
    }
}
