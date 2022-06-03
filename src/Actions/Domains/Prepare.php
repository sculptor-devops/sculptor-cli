<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Stages\Certificates;
use Sculptor\Agent\Actions\Domains\Stages\Bin;
use Sculptor\Agent\Actions\Domains\Stages\Crontab;
use Sculptor\Agent\Actions\Domains\Stages\Env;
use Sculptor\Agent\Actions\Domains\Stages\Logrotate;
use Sculptor\Agent\Actions\Domains\Stages\Permissions;
use Sculptor\Agent\Actions\Domains\Stages\Services;
use Sculptor\Agent\Actions\Domains\Stages\WebServer;
use Sculptor\Agent\Actions\Domains\Stages\Worker;
use Sculptor\Agent\Actions\Domains\Stages\Deploy;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Actions\Domains\Support\Stage;
use Sculptor\Agent\Enums\DomainStatusType;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Prepare extends Stage
{
    use Logging;

    protected array $stages = [
        Env::class,
        Certificates::class,
        Crontab::class,
        Worker::class,
        Bin::class,
        WebServer::class,
        Logrotate::class,
        Deploy::class,
        Permissions::class,
        Services::class
    ];

    /**
     * @throws Exception
     */
    public function run(string $name): void
    {
        $options = [ 'certbot.hook' => 'register'];

        $domain = $this->domains->find($name);

        $this->info($domain, $options);

        foreach ($this->stages as $stage) {
            $step = $this->make($stage);

            $options = $step->prepare($domain, $options);
        }
    }
}
