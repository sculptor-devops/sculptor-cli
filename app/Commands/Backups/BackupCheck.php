<?php

namespace App\Commands\Backups;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Sculptor\Agent\Actions\Backups\Check;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Support\Command\Base;

class BackupCheck extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'backup:check {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check backup archive';

    public function __construct(private Backups $backups)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Check $check
     * @return int
     * @throws Exception
     */
    public function handle(Check $check): int
    {
        $name = $this->argument('name');

        $this->startTask("Checking backup $name");

        if (!$check->run($name)) {
            return $this->errorTask("Error: {$check->error()}");
        }

        return $this->completeTask();
    }

    /**
     * @throws Exception
     */
    public function schedule(Schedule $schedule): void
    {
        foreach ($this->backups->runnable() as $backup) {
            $schedule->command(static::class, ['name' => $backup->name])->cron('0 1 * * *');
        }
    }
}
