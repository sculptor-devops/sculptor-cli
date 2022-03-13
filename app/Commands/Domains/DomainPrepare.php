<?php

namespace App\Commands\Domains;

use Exception;
use Illuminate\Encryption\Encrypter;
use Sculptor\Agent\Actions\Domains\Prepare;
use Sculptor\Agent\Support\Chronometer;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainPrepare extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:prepare {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Prepare a domain compiling all configuration files';

    /**
     * Execute the console command.
     *
     * @param Prepare $prepare
     * @return int
     * @throws Exception
     */
    public function handle(Prepare $prepare): int
    {
        $name = $this->argument('name');

        $this->startTask("Preparing {$name}..");

        $timer = Chronometer::start();

        $prepare->run($name);

        $this->completeTask();

        return 0;
    }
}
