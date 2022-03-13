<?php

namespace App\Commands\Backups;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Sculptor\Agent\Actions\Backups\Make;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupMake extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'backup:make {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Make backups';

    public function __construct(private Backups $backups)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Make $make
     * @return int
     * @throws Exception
     */
    public function handle(Make $make): int
    {
        $name = $this->argument('name');

        $this->startTask("Running backup $name..");

        if (!$make->run($name)) {
            return $this->errorTask('Error: See logs for details');
        }

        return $this->completeTask();
    }

    /**
     * @throws Exception
     */
    public function schedule(Schedule $schedule): void
    {
        foreach ($this->backups->runnable() as $backup) {
            $schedule->command(static::class, [ 'name' => $backup->name()])->cron($backup->cron);
        }
    }
}
