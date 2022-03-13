<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Actions\Alarms\Factories\Rearms;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class AlarmRearmMethod
{
    public function __construct(private Rearms $rearms)
    {
        //
    }

    public function rule(): array
    {
        return [
            'rearm_method' => [
                'required',
                'max:255',
                'in:' . join(',', $this->rearms->keys())
            ]
        ];
    }
}
