<?php

namespace Sculptor\Agent\Monitors;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Monitors\Contracts\Monitor;
use Sculptor\Agent\Monitors\System\Cpu;
use Sculptor\Agent\Monitors\System\Disk;
use Sculptor\Agent\Monitors\System\Io;
use Sculptor\Agent\Monitors\System\Memory;
use Sculptor\Agent\Monitors\System\Uptime;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Collector
{
    public function __construct(private array $drivers, private Configuration $configuration)
    {
        //
    }

    /**
     * @throws BindingResolutionException
     */
    public function all(): array
    {
        $result = [];

        foreach ($this->drivers as $monitor) {
            foreach ($monitor->values($this->configuration) as $key => $value) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function find(string $name)
    {
        foreach ($this->all() as $key => $value) {
            if ($key == $name) {
                return $value;
            }
        }

        throw new Exception("Monitor $name not found");
    }
}
