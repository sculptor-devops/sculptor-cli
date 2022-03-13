<?php

namespace App\Commands\Sculptor;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Logs\Upgrades\Upgrades;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class SculptorMigrate extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sculptor:migrate';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sculptor system migrate from previous version';

    /**
     * Execute the console command.
     * @param Configuration $configuration
     * @return int
     * @throws Exception
     */
    public function handle(Configuration $configuration): int
    {
        $current = $configuration->version();

        switch ($current) {
            case SCHEMA_VERSION:
                $this->info("No need for upgrades from schema $current");

                return 0;

            default:
                throw new Exception("Unknown configuration schema $current");
        }
    }
}
