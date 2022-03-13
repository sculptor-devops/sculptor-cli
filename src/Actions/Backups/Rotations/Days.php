<?php

namespace Sculptor\Agent\Actions\Backups\Rotations;

use Sculptor\Agent\Actions\Backups\Contracts\Rotation;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Days implements Rotation
{
    public function name(): string
    {
        return 'days';
    }

    public function rotate(array $catalogs, int $number): array
    {
        $catalogs = collect($catalogs)
            ->sortBy(fn($item) => $item->timestamp());

        $pivot = now()->addDays($number)->timestamp;

        return $catalogs
            ->filter(fn($item) => $pivot < $item->timestamp())
            ->toArray();
    }
}
