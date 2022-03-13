<?php

namespace App\Commands\Backups;

use Exception;
use Lorisleiva\CronTranslator\CronParsingException;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Support\Command\Base;
use Lorisleiva\CronTranslator\CronTranslator;

class BackupDelete extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'backup:delete {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete backups';

    /**
     * Execute the console command.
     *
     * @param Backups $backups
     * @return int
     * @throws Exception
     */
    public function handle(Backups $backups): int
    {
        $name = $this->argument('name');

        $this->startTask("Deleting backup $name");

        $backups->delete($name);

        return $this->completeTask();
    }
}
