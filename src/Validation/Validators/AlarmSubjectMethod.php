<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Actions\Alarms\Factories\Subjects;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class AlarmSubjectMethod
{
    public function __construct(private Subjects $subjects)
    {
        //
    }

    public function rule(): array
    {
        return [
            'subject_method' => [
                'required',
                'max:255',
                'in:' . join(',', $this->subjects->keys())
            ]
        ];
    }
}
