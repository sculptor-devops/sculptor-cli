<?php

namespace App\Commands\Daemons;

use Exception;
use Sculptor\Agent\Actions\Daemons\Operation;
use Sculptor\Agent\Support\Command\Base;

class DaemonEnable extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'daemon:enable {group}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Enable system daemon group';

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

        $this->startTask("Enabling group {$group}");

        try {
            $operation->run('enable', $group);
        } catch (Exception $e) {
            return $this->errorTask($e->getMessage());
        }

        return $this->completeTask();
    }
}
