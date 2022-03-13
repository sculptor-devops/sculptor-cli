<?php

namespace App\Commands\Backups;

use Exception;
use Sculptor\Agent\Actions\Backups\Create;
use Sculptor\Agent\Support\Command\Base;

class BackupCreate extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'backup:create {name} {resource} {target}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a backup for a resource';

    /**
     * Execute the console command.
     *
     * @param Create $create
     * @return int
     * @throws Exception
     */
    public function handle(Create $create): int
    {
        $name = $this->argument('name');

        $resource = $this->argument('resource');

        $target = $this->argument('target');

        $this->startTask("Creating backup $name: $resource $target");

        $create->run($name, $resource, $target);

        return $this->completeTask();
    }
}
