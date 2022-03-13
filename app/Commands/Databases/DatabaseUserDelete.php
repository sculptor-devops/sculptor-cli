<?php

namespace App\Commands\Databases;

use Exception;
use Sculptor\Agent\Actions\Databases\UserDelete;
use Sculptor\Agent\Support\Command\Base;

class DatabaseUserDelete extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:user_delete {database} {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete a database user';

    /**
     * Execute the console command.
     *
     * @param UserDelete $user
     * @return int
     * @throws Exception
     */
    public function handle(UserDelete $user): int
    {
        $database = $this->argument('database');

        $name = $this->argument('name');

        $this->startTask("Deleting user $name");

        $user->run($database, $name);

        return $this->completeTask();
    }
}
