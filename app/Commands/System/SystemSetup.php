<?php

namespace App\Commands\System;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Command\Base;

class SystemSetup extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'system:setup {name} {value}}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Manage system configuration';

    /**
     * Execute the console command.
     *
     * @param Configuration $configuration
     * @return int
     * @throws Exception
     */
    public function handle(Configuration $configuration): int
    {
        $name = $this->argument('name');

        $value = (string)$this->argument('value');

        return $this->set($configuration, $name, $value);
    }

    /**
     * @throws Exception
     */
    private function set(Configuration $configuration, string $name, string $value): int
    {
        if ($name == null || $value == null) {
            $this->error("Name and value cannot be null");

            return 1;
        }

        $this->startTask("Set {$name}={$value}");

        $configuration->set($name, $value);

        $configuration->save();

        return $this->completeTask();
    }
}
