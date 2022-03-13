<?php

namespace App\Commands\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Certificate;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainCertbot extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:certbot {name} {hook=post}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Domain certbot hooks';

    /**
     * Execute the console command.
     * @param Certificate $certificates
     * @return int
     * @throws Exception
     */
    public function handle(Certificate $certificates): int
    {
        $name = $this->argument('name');

        $hook = $this->argument('hook');

        $this->startTask("Certbot {$name} hook {$hook}");

        $certificates->run($name, $hook);

        return $this->completeTask();
    }
}
