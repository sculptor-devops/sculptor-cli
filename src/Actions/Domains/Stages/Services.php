<?php

namespace Sculptor\Agent\Actions\Domains\Stages;

use Exception;
use Sculptor\Agent\Actions\Contracts\Domain as DomainInterface;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Repositories\Entities\Domain as Entity;
use Sculptor\Agent\Actions\Support\Daemons;
use Sculptor\Foundation\Services\Daemons as Service;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Services implements DomainInterface
{
    use Logging;

    private array $groups = [
        DaemonGroupType::WEB,
        DaemonGroupType::QUEUE
    ];

    public function __construct(private Service $service, private Daemons $daemons)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function create(Entity $domain, array $options = null): array
    {
        throw new Exception("Create not implemented in services stage");
    }

    public function delete(Entity $domain, array $options = null): array
    {
        return $this->reload($domain, $options);
    }

    public function prepare(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'prepare');

        return $this->reload($domain, $options);
    }

    public function deploy(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'deploy');

        return $this->reload($domain, $options);
    }

    private function reload(Entity $domain, array $options = null): array
    {
        foreach ($this->daemons->only($this->groups) as $daemon) {
            $this->debug($domain, $options, "reload {$daemon->name()}");

            $this->service->reload($daemon->name());
        }

        return $options ?? [];
    }
}
