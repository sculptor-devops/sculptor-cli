<?php

namespace Sculptor\Agent\Actions\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Service
{
    function name(): string;

    function package(): string;

    function group(): string;
}
