<?php

namespace Sculptor\Agent\Actions\Backups\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
interface Rotation
{
    public function name(): string;

    public function rotate(array $catalogs, int $number): array;

    // public function clean(Backup $backup): bool;

    // public function check(Backup $backup): bool;
}
