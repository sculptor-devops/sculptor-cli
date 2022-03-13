<?php

namespace App\Commands\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Create;
use Sculptor\Agent\Support\Command\Base;

class DomainCreate extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:create {name} {template=laravel} {--force}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a domain';

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

        $template = $this->argument('template');

        $force = $this->option('force');

        $this->startTask("Creating domain $name: $template");

        $create->run($name, $template, $force);

        return $this->completeTask();
    }
}
