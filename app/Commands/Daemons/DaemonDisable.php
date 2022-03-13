<?php

namespace App\Commands\Daemons;

use Exception;
use Sculptor\Agent\Actions\Daemons\Operation;
use Sculptor\Agent\Support\Command\Base;

class DaemonDisable extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'daemon:disable {group}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Disable system daemon group';

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

        $this->startTask("Disabling group {$group}");

        try {
            $operation->run('disable', $group);
        } catch (Exception $e) {
            return $this->errorTask($e->getMessage());
        }

        return $this->completeTask();
    }
}
