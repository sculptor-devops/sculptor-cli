<?php

namespace App\Commands\Alarms;


use Exception;
use Sculptor\Agent\Actions\Alarms\Setup;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class AlarmSetup extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'alarm:setup {name} {key} {value}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Setup and alarm';

    /**
     * @throws Exception
     */
    public function handle(Setup $setup): int
    {
        // $input = file_get_contents('php://stdin');

        $name = $this->argument('name');

        $key = $this->argument('key');

        $value = $this->argument('value');

        $this->startTask("Setup alarm $name $key => $value");

        $setup->run($name, $key, $value);

        return  $this->completeTask();
    }
}
