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
    public function name(): string;
    public function register(Domain $domain, int $days = 3650): void;
    public function pre(Domain $domain): void;
    public function deploy(Domain $domain): void;
    public function delete(Domain $domain): void;
    public function files(Domain $domain): array;
}
