<?php

namespace Sculptor\Agent\Actions\Backups\Contracts;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

interface Dumper
{
    public function dump(string $name, string $filename): bool;

    public function name(): string;
}
