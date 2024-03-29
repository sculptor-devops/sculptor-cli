<?php

namespace Sculptor\Agent\Validation\Contracts;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface ValidatorRule
{
    public function rule(): array;
}
