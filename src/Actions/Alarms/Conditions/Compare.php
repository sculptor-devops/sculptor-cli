<?php

namespace Sculptor\Agent\Actions\Alarms\Conditions;

use Exception;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Sculptor\Agent\Actions\Alarms\Support\Converter;
use Sculptor\Agent\Actions\Alarms\Support\Validable;
use Sculptor\Agent\Actions\Alarms\Contracts\Condition;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Compare extends Validable implements Condition
{
    public array $properties = [
        'threshold',
        'condition'
    ];

    function name(): string
    {
        return 'compare';
    }

    /**
     * @throws Exception
     */
    function check(float $current, float $last): bool
    {
        $threshold = Converter::from($this->parameters->get('threshold'));

        $condition = Str::of($this->parameters->get('condition'))->lower() . '';

        return match ($condition) {
            'greater', 'gt' => $current > $threshold,
            'less', 'lt' => $current < $threshold,
            'equal', 'eq' => $current == $threshold,
            'greaterequal', 'gte' => $current >= $threshold,
            'lessequal', 'lte' => $current <= $threshold,
            'different', 'dif' => $current != $threshold,
            default => Throw new InvalidArgumentException("Invalid compare condition $condition")
        };
    }

    function parameters(Parameters $parameters): Condition
    {
        parent::parameters($parameters);

        return $this;
    }
}
