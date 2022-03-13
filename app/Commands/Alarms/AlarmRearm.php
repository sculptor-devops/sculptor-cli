<?php

namespace App\Commands\Alarms;

use Exception;
use Sculptor\Agent\Repositories\Alarms;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class AlarmRearm extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'alarm:rearm {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Rearm an alarm';

    /**
     * @throws Exception
     */
    public function handle(Alarms $alarms): int
    {
        $name = $this->argument('name');

        $this->startTask("Rearm alarm $name");

        $alarm = $alarms->find($name);

        $alarm->statusPreviousBool = false;

        $alarm->statusAlarmedBool = false;

        $alarm->save([ 'status.at' => '', 'status.last' => '' ]);

        return  $this->completeTask();
    }
}
