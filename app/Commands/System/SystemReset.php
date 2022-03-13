<?php

namespace App\Commands\System;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Support\Folders;

class SystemReset extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'system:reset {--force}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Reset system configuration';

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
        if (!$this->askYesNo('All configurations will be set to default and lose actual values, continue?', $this->option('force'))) {
            $this->warn('Operation skipped.');

            return 1;
        }

        $this->startTask("Reset configurations to default");

        $folders->configuration(true);

        return $this->completeTask();
    }
}
