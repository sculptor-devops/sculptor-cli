<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Validation\Contracts\ValidatorRule;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class AlarmName implements ValidatorRule
{
    public function rule(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
                'alpha_dash'
            ]
        ];
    }
}
