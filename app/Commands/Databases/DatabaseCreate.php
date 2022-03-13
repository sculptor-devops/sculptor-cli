<?php

namespace App\Commands\Databases;

use Exception;
use Sculptor\Agent\Actions\Databases\Create;
use Sculptor\Agent\Support\Command\Base;

class DatabaseCreate extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:create {name} {driver=mysql}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a database';

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

        $driver = $this->argument('driver');

        $this->startTask("Creating database $name: $driver");

        $create->run($name, $driver);

        return $this->completeTask();
    }
}
