<?php

namespace App\Commands\Databases;

use Exception;
use Sculptor\Agent\Repositories\Databases;
use Sculptor\Agent\Support\Command\Base;

class DatabaseShow extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:show';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show databases';

    /**
     * Execute the console command.
     *
     * @param Databases $databases
     * @return int
     * @throws Exception
     */
    public function handle(Databases $databases): int
    {
        $all = $databases->all();

        $this->showAll($all);

        return 0;
    }

    private function showAll(array $all): void
    {
        $tabled = [];

        $count = count($all);

        foreach ($all as $database) {
            $tabled[] = [
                $database->name(),
                $database->driver,
                collect($database->users())->map(fn($user) => $user->name)->join(', ')
            ];
        }

        $this->table(['Name', 'Driver', 'Users'], $tabled);

        $this->info("{$count} databases");
    }
}
