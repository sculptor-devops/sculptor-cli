<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Actions\Backups\Factories\Archives;
use Sculptor\Agent\Validation\Contracts\ValidatorRule;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupArchiveDriver implements ValidatorRule
{
    public function __construct(private Archives $archives)
    {
        //
    }

    public function rule(): array
    {
        return [
            'archive_driver' => [
                'required',
                'max:255',
                'in:' . join(',', $this->archives->keys())
            ]
        ];
    }
}
