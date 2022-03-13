<?php

namespace Sculptor\Agent\Monitors\System;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Monitors\Contracts\Monitor;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Disk implements Monitor
{
    public function values(Configuration $configuration): array
    {
        $result = [];

        foreach ($configuration->getArray('monitors.disks') as $disk) {
            $result += $this->disk($disk['root'], $disk['name']);
        }

        return $result;
    }

    private function disk(string $root, string $device): array
    {
        try {
            return [
                "{$this->name()}.free.{$device}" => disk_free_space($root),
                "{$this->name()}.total.{$device}" => disk_total_space($root),
            ];
        } catch (Exception $e) {
            return [
                "{$this->name()}.free.{$device}" => 0,
                "{$this->name()}.total.{$device}" => 0,
            ];
        }
    }

    public function name(): string
    {
        return 'disk';
    }
}
