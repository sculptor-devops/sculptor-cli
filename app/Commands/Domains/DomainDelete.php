<?php

namespace App\Commands\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Delete;
use Sculptor\Agent\Support\Command\Base;

class DomainDelete extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:delete {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete a domain';

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

        $this->startTask("Deleting domain $name");

        $delete->run($name);

        return $this->completeTask();
    }
}
