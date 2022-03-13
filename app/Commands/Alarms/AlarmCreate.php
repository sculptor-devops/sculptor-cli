<?php

namespace App\Commands\Alarms;

use Exception;
use Sculptor\Agent\Actions\Alarms\Create;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class AlarmCreate extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'alarm:create {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create and alarm';

    /**
     * @throws Exception
     */
    public function handle(Create $create): int
    {
        $name = $this->argument('name');

        $this->startTask("Creating alarm $name");

        $create->run($name);

        return  $this->completeTask();
    }
}
