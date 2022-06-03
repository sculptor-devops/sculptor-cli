<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Actions\Backups\Factories\Rotations;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupRotationPolicy
{
    public function __construct(private Rotations $rotations)
    {
        //
    }

    public function rule(): array
    {
        return [
            'rotation_policy' => [
                'required',
                'max:255',
                'in:' . join(',', $this->rotations->keys())
            ]
        ];
    }
}
