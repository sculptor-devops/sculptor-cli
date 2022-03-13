<?php

namespace App\Commands\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Deploy;
use Sculptor\Agent\Support\Chronometer;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainDeploy extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:deploy {name} {task?} {--force} {--skip-prepare}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Deploy a domain (custom command can be specified)';

    /**
     * Execute the console command.
     *
     * @param Deploy $deploy
     * @return int
     * @throws Exception
     */
    public function handle(Deploy $deploy): int
    {
        $name = $this->argument('name');

        $task = $this->argument('task');

        $force = $this->option('force');

        $skipPrepare = $this->option('skip-prepare');

        $command = $deploy->command($name, $task);

        $timer = Chronometer::start();

        if (!$skipPrepare) {
            $this->call('domain:prepare', ['name' => $name]);
        }

        $this->startTask("Deploy $name ({$command})..");

        $deploy->run($name, $command, $force);

        $this->completeTask();

        return 0;
    }
}
