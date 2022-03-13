<?php

namespace Sculptor\Agent\Actions\Contracts;

use Sculptor\Agent\Repositories\Entities\Domain;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Certificate
{
    function name(): string;
    function register(Domain $domain, int $days = 3650): void;
    function pre(Domain $domain): void;
    function deploy(Domain $domain): void;
    function delete(Domain $domain): void;
    function files(Domain $domain): array;
}
