<?php

namespace App\Commands\Sculptor;

use Exception;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Support\Upgrade as UpgradeService;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class SculptorUpgrade extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sculptor:upgrade {--check}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sculptor system upgrades';

    /**
     * Execute the console command.
     * @param UpgradeService $upgrade
     * @return int
     * @throws Exception
     */
    public function handle(UpgradeService $upgrade): int
    {
        $current = $upgrade->current();

        $online = $upgrade->online();

        if ($this->option('check')) {
            return $this->infoTask("Current version $current, found version $online online", $upgrade->available());
        }

        if (!$upgrade->available()) {
            return $this->infoTask("Current $current version is already at latest", true);
        }

        $this->startTask("Upgrading from $current to $online");

        $upgrade->run();

        $this->completeTask();

        $this->call('sculptor:migrate');

        return 0;
    }
}
