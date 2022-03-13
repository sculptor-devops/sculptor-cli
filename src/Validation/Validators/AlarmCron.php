<?php

namespace Sculptor\Agent\Validation\Validators;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use App\Rules\Cron;
use Sculptor\Agent\Validation\Contracts\ValidatorRule;

class AlarmCron implements ValidatorRule
{
    public function rule(): array
    {
        return [
            'cron' => [
                'required',
                'max:255',
                new Cron()
            ]
        ];
    }
}
