<?php

namespace App\Commands\Databases;

use Exception;
use Sculptor\Agent\Actions\Databases\Delete;
use Sculptor\Agent\Support\Command\Base;

class DatabaseDelete extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:delete {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete a database';

    /**
     * Execute the console command.
     *
     * @param Delete $delete
     * @return int
     * @throws Exception
     */
    public function handle(Delete $delete): int
    {
        $name = $this->argument('name');

        $this->startTask("Deleting database $name");

        $delete->run($name);

        return $this->completeTask();
    }
}
