<?php

namespace Sculptor\Agent\Actions\Daemons;

use Sculptor\Agent\Actions\Support\Daemons;
use Sculptor\Foundation\Services\Daemons as Services;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Status
{
    private Services $services;

    private Daemons $daemons;

    public function __construct(Services $services, Daemons $daemons)
    {
        $this->services = $services;

        $this->daemons = $daemons;
    }

    public function run(): array
    {
        $result = [];

        foreach ($this->daemons->all() as $service) {
            $active = $this->services->active($service->name());

            $installed = $this->services->installed($service->package());

            $result[] = ['group' => $service->group(), 'name' => $service->name(), 'active' => $active, 'installed' => $installed];
        }

        return $result;
    }
}
