<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Actions\Alarms\Factories\Methods;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class AlarmActionMethod
{
    public function __construct(private Methods $methods)
    {
        //
    }

    public function rule(): array
    {
        return [
            'action_method' => [
                'required',
                'max:255',
                'in:' . join(',', $this->methods->keys())
            ]
        ];
    }
}
