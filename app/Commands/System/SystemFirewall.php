<?php

namespace App\Commands\System;

use Exception;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Foundation\Services\Firewall;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class SystemFirewall extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:firewall {operation=show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System firewall';

    /**
     * Execute the console command.
     *
     * @param Firewall $firewall
     * @return int
     * @throws Exception
     */
    public function handle(Firewall $firewall): int
    {
        $operation = $this->argument('operation');

        $this->startTask("Firewall {$operation}");

        switch ($operation) {
            case 'show':
                $this->completeTask();

                $this->info('Active ' . $this->yesNo($firewall->status()));

                $this->table(['Index', 'Rule'], $firewall->list());

                return 0;

            case 'enable':
                if (!$firewall->enable()) {
                    return $this->errorTask($firewall->error());
                }

                return $this->completeTask();

            case 'disable':
                if (!$firewall->disable()) {
                    return $this->errorTask($firewall->error());
                }

                return $this->completeTask();

            case 'reset':
                if (!$firewall->reset()) {
                    return $this->errorTask($firewall->error());
                }

                return $this->completeTask();
        }

        $this->errorTask("Unknown operation {$operation}");

        return 1;
    }
}
