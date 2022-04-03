<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Stages\Permissions;
use Sculptor\Agent\Actions\Domains\Stages\Services;
use Sculptor\Agent\Actions\Domains\Stages\Deploy as DeployStage;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Actions\Domains\Support\Stage;
use Sculptor\Agent\Enums\DomainStatusType;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Deploy extends Stage
{
    use Logging;

    protected array $stages = [
        DeployStage::class,
        Permissions::class,
        Services::class
    ];

    /**
     * @throws Exception
     */
    public function run(string $name, string $task = null, bool $force = false): void
    {
        $options = [ 'task' => $task, 'force' => $force ];

        $domain = $this->domains->find($name);

        $this->info($domain, $options);

        $domain->save([ 'status' => DomainStatusType::DEPLOYING ]);

        foreach ($this->stages as $stage) {
            $step = $this->make($stage);

            $options = $step->deploy($domain, $options);
        }

        $domain->save([
            'deployed' => now(),
            'status' => DomainStatusType::DEPLOYED
        ]);
    }

    /**
     * @throws Exception
     */
    public function command(string $name, ?string $task): string
    {
        $domain = $this->domains->find($name);

        $command = $domain->deployCommand;

        if ($domain->status == DomainStatusType::NEW) {
            $command = $domain->deployInstall;
        }

        if ($task) {
            $command = $task;
        }

        return $command;
    }
}
