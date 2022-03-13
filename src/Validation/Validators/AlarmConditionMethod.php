<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Actions\Alarms\Factories\Conditions;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class AlarmConditionMethod
{
    public function __construct(private Conditions $conditions)
    {
        //
    }

    public function rule(): array
    {
        return [
            'condition_method' => [
                'required',
                'max:255',
                'in:' . join(',', $this->conditions->keys())
            ]
        ];
    }
}
