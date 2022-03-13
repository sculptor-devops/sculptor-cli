<?php


namespace Sculptor\Agent\Actions\Domains\Stages;


use Exception;
use Sculptor\Agent\Actions\Contracts\Domain as DomainInterface;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Support\System;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Permissions implements DomainInterface
{
    use Logging;

    public function __construct(private System $system)
    {
        //
    }

    /**
     * @throws Exception
     */
    private function run(Domain $domain): void
    {
        $user = $domain->user;

        // Logs::actions()->debug("Permissions setup for {$root} user {$user}");

        $this->system->chmod($domain->root(), '755', true);

        $this->system->chown($domain->root(), $user, true);
    }

    /**
     * @throws Exception
     */
    public function create(Domain $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'create');

        $this->run($domain);

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function delete(Domain $domain, array $options = null): array
    {
        throw new Exception("Delete not implemented in permission stage");
    }

    /**
     * @throws Exception
     */
    public function prepare(Domain $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'prepare');

        $this->run($domain);

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function deploy(Domain $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'deploy');

        $this->run($domain);

        return $options ?? [];
    }
}
