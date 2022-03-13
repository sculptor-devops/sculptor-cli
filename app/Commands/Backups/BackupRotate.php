<?php

namespace App\Commands\Backups;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Sculptor\Agent\Actions\Backups\Rotate;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupRotate extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'backup:rotate {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Rotate backups';

    public function __construct(private Backups $backups)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Rotate $rotate
     * @return int
     * @throws Exception
     */
    public function handle(Rotate $rotate): int
    {
        $name = $this->argument('name');

        $this->startTask("Rotating backup $name..");

        $rotated = $rotate->run($name);

        if ($rotated >= 0) {
            $this->completeTask();

            $this->info("Rotated $rotated archives");

            return 0;
        }

        return $this->errorTask('Error: see logs for details');
    }

    /**
     * @throws Exception
     */
    public function schedule(Schedule $schedule): void
    {
        foreach ($this->backups->runnable() as $backup) {
            $schedule->command(static::class, ['name' => $backup->name])->cron($backup->rotationCron);
        }
    }
}
