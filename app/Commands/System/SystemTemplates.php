<?php

namespace App\Commands\System;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Support\Folders;

class SystemTemplates extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'system:templates';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show system templates';

    /**
     * Execute the console command.
     *
     * @param Folders $folders
     * @param Configuration $configuration
     * @return int
     * @throws Exception
     */
    public function handle(Folders $folders, Configuration $configuration): int
    {
        $this->info("Current file {$configuration->fileName()}");

        if (!$this->askYesNo('All templates will be set to default and lose actual values, continue?', $force)) {
            $this->warn('Operation skipped.');

            return 1;
        }

        $this->startTask("Default templates copied");

        $folders->templates(true);

        return $this->completeTask();
    }
}
