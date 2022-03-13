<?php


namespace Sculptor\Agent\Validation\Validators;


use App\Rules\Cron;
use Sculptor\Agent\Validation\Contracts\ValidatorRule;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupRotationCron implements ValidatorRule
{
    public function rule(): array
    {
        return [
            'rotation_cron' => [
                'required',
                'max:255',
                new Cron()
            ]
        ];
    }
}
