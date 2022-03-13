<?php

namespace App\Commands\Daemons;

use Exception;
use Sculptor\Agent\Actions\Daemons\Operation;
use Sculptor\Agent\Support\Command\Base;

class DaemonReload extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'daemon:reload {group}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Reload system daemon group';

    /**
     * Execute the console command.
     *
     * @param Operation $operation
     * @return int
     * @throws Exception
     */
    public function handle(Operation $operation): int
    {
        $group = $this->argument('group');

        $this->startTask("Reloading group {$group}");

        try {
            $operation->run('reload', $group);
        } catch (Exception $e) {
            return $this->errorTask($e->getMessage());
        }

        return $this->completeTask();
    }
}
