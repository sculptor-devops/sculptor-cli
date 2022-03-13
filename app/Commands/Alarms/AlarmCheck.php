<?php

namespace App\Commands\Alarms;

use Exception;
use Sculptor\Agent\Actions\Alarms\Check;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class AlarmCheck extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'alarm:check {name} {--dry}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check an alarm';

    /**
     * @throws Exception
     */
    public function handle(Check $check): int
    {
        $name = $this->argument('name');

        $dry = $this->option('dry');

        $this->startTask("Checking alarm $name");

        if ($check->run($name, $dry)) {
            return $this->errorTask($check->error());
        }

        return $this->completeTask();
    }
}
