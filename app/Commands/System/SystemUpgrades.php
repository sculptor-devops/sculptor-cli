<?php

namespace App\Commands\System;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Sculptor\Agent\Logs\Upgrades\Upgrades;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class SystemUpgrades extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'system:upgrades {operation=list}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show system upgrades';

    /**
     * Execute the console command.
     * @param Upgrades $upgrades
     * @return int
     * @throws Exception
     */
    public function handle(Upgrades $upgrades): int
    {
        $operation = $this->argument('operation');

        switch ($operation) {
            case 'list':
                $this->list($upgrades);

                return 0;

            case 'check':
                $this->check($upgrades);

                $this->warn('Upgrade check dispatched, check logs for further information');

                return 0;
        }

        $index = intval($operation);

        if ($index > 0) {
            $this->show($upgrades, $index);

            return 0;
        }

        return 1;
    }

    /**
     * @param Upgrades $logs
     * @throws Exception
     */
    private function list(Upgrades $logs): void
    {
        $index = 1;

        $events = [];

        $recently = false;

        foreach ($logs->events() as $event) {
            $packages = count($logs->parse($event)
                ->packages());

            $events[] = ['index' => $index, 'upgrade' => $event->toString(), 'packages' => $packages];

            if ($event->isYesterday() || $event->isToday()) {
                $recently = true;
            }

            $index++;
        }

        $this->table(['Index', 'Event', 'Packages'], $events);

        $this->info('Use system:upgrades <INDEX> to show complete event');

        if (!$logs->active()) {
            $this->warn('Automatic upgrades are not active.');
        }

        if ($recently) {
            $this->warn('The system were upgraded recently, see log for details.');
        }
    }

    /**
     * @param Upgrades $logs
     * @param int $index
     * @throws Exception
     */
    private function show(Upgrades $logs, int $index): void
    {
        $result = [];

        $upgraded = [];

        $events = $logs->events();

        $log = $logs->parse($events[$index - 1]);

        $packages = $log->packages();

        foreach ($packages as $package) {
            $upgraded[] = [$package];
        }

        foreach ($log as $row) {
            $result[] = [$row];
        }

        $this->table([], $result);

        $this->info("Package upgraded");

        $this->table([], $upgraded);

        $this->info("Between {$log->start()} and {$log->end()}");
    }

    /**
     * @throws Exception
     */
    private function check(Upgrades $upgrades): void
    {
        if (count($upgrades->events()) == 0) {
            return;
        }

        $event = $upgrades->last();

        if ($event->recent()) {
            $packages = implode(', ', $event->packages());

            // Logs::security()->alert("System unattended upgrades {$packages}");
        }
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class, ['check'])->dailyAt('23:59');
    }
}
