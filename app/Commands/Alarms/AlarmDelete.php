<?php

namespace App\Commands\Alarms;


use Exception;
use Sculptor\Agent\Actions\Alarms\Delete;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class AlarmDelete extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'alarm:delete {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete and alarm';

    /**
     * @throws Exception
     */
    public function handle(Delete $delete): int
    {
        $name = $this->argument('name');

        $this->startTask("Deleting alarm $name");

        $delete->run($name);

        return  $this->completeTask();
    }
}
