<?php

namespace Sculptor\Agent\Validation\Validators;

use App\Rules\Engine;
use App\Rules\Vcs;
use Sculptor\Agent\Support\Version\Php;
use Sculptor\Agent\Validation\Contracts\ValidatorRule;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainEngine implements ValidatorRule
{
    public function __construct(private Php $version)
    {
    }

    public function rule(): array
    {
        return [
            'engine' => [
                'required',
                new Engine($this->version)
            ]
        ];
    }
}
