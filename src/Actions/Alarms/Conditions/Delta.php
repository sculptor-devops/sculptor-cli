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

class Delta extends Validable implements Condition
{
    public array $properties = [
      'threshold',
      'condition'
    ];

    public function name(): string
    {
        return 'delta';
    }

    /**
     * @throws Exception
     */
    public function check(float $current, float $last): bool
    {
        $threshold = Converter::from($this->parameters->get('threshold'));

        $condition = Str::of($this->parameters->get('condition'))->lower() . '';

        $delta = (($current - $last) / $last) * 100;

        return match ($condition) {
            'greater', 'gt' => $delta > $threshold,
            'less', 'lt' => $delta < $threshold,
            'equal', 'eq' => $delta == $threshold,
            'greaterequal', 'gte' => $delta >= $threshold,
            'lessequal', 'lte' => $delta <= $threshold,
            'different', 'dif' => $delta != $threshold,
            default => throw new InvalidArgumentException("Invalid delta condition $condition")
        };
    }

    public function parameters(Parameters $parameters): Condition
    {
        parent::parameters($parameters);

        return $this;
    }
}
