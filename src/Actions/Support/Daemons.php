<?php

namespace Sculptor\Agent\Actions\Support;

use Sculptor\Agent\Support\Version\Php;
use Sculptor\Agent\Actions\Daemons\Services\Php as PhpService;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Daemons
{
    public function __construct(private Php $php, private array $drivers)
    {
        foreach ($this->php->available() as $version) {
            $php = new PhpService($version);

            $this->drivers[] = $php;
        }
    }

    public function all(): array
    {
        return $this->drivers;
    }

    public function only(array $groups): array
    {
        $result = [];

        foreach ($this->all() as $daemon) {
            if (!in_array($daemon->group(), $groups)) {
                continue;
            }

            $result[] = $daemon;
        }

        return $result;
    }

    public function valid(string $group): bool
    {
        foreach ($this->all() as $daemon) {
            if ($daemon->group() == $group) {
                return true;
            }
        }

        return false;
    }
}
