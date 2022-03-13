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
class SculptorInit extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sculptor:init';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sculptor init procedure';

    /**
     * Execute the console command.
     * @param Configuration $configuration
     * @return int
     * @throws Exception
     */
    public function handle(Configuration $configuration): int
    {
        // default

        return 0;
    }
}
