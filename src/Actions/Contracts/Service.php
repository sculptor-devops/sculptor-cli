<?php

namespace Sculptor\Agent\Actions\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Service
{
    public function name(): string;

    public function package(): string;

    public function group(): string;
}
