<?php

namespace Sculptor\Agent\Actions\Backups\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Strategy
{
    public function name(): string;

    public function create(string $target): array;

    public function meta(string $target): array;
}
