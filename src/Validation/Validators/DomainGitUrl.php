<?php

namespace Sculptor\Agent\Validation\Validators;

use App\Rules\Vcs;
use Sculptor\Agent\Validation\Contracts\ValidatorRule;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainGitUrl implements ValidatorRule
{
    public function rule(): array
    {
        return [
            'git_url' => [
                'required',
                'max:1024',
                new Vcs()
            ]
        ];
    }
}
