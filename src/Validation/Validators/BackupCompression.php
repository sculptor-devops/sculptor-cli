<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Actions\Backups\Factories\Compressions;
use Sculptor\Agent\Validation\Contracts\ValidatorRule;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupCompression implements ValidatorRule
{
    public function __construct(private Compressions $compressors)
    {
        //
    }

    public function rule(): array
    {
        return [
            'compression' => [
                'required',
                'max:255',
                'in:' . join(',', $this->compressors->keys())
            ]
        ];
    }
}
