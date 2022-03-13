<?php

namespace App\Commands\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Crontab;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainCrontab extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:crontab {name} {operation=show} {schedule?} {shell?} {--skip-prepare}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit domain crontab';

    /**
     * Execute the console command.
     *
     * @param Crontab $crontab
     * @return int
     * @throws Exception
     */
    public function handle(Crontab $crontab): int
    {
        $name = $this->argument('name');

        $operation = $this->argument('operation');

        $schedule = $this->argument('schedule');

        $command = $this->argument('shell');

        $skipPrepare = $this->option('skip-prepare');

        switch ($operation) {
            case 'show':
                $this->table(['Index', 'Schedule', 'Command'], $crontab->all($name));

                return 0;

            case 'add':
                $this->startTask("Add crontab {$name} on {$schedule} command {$command}..");

                $crontab->run($name, $schedule, $command);

                break;

            case 'del':
                $this->startTask("Delete crontab {$name} at {$schedule}..");

                $crontab->delete($name, $schedule);

                break;
            default:
                throw new Exception("Invalid operation $operation");
        }

        $this->completeTask();

        if (!$skipPrepare) {
            $this->call('domain:prepare', ['name' => $name]);
        }

        return 0;
    }
}
