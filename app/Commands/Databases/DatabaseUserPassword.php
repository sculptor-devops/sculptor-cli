<?php

namespace App\Commands\Databases;

use Exception;
use Sculptor\Agent\Actions\Databases\User;
use Sculptor\Agent\Support\Command\Base;

class DatabaseUserPassword extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:user_password {database} {name} {password?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Change database user password';

    /**
     * Execute the console command.
     *
     * @param User $user
     * @return int
     * @throws Exception
     */
    public function handle(User $user): int
    {
        $database = $this->argument('database');

        $name = $this->argument('name');

        $password = $this->argument('password');

        $this->startTask("Creating database user $name");

        $password = $user->run($database, $name, $password);

        $this->completeTask();

        $this->info("User has password: $password");

        return 0;
    }
}
