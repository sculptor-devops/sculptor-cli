<?php


namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Stages\Certificates;
use Sculptor\Agent\Actions\Domains\Stages\Crontab;
use Sculptor\Agent\Actions\Domains\Stages\Logrotate;
use Sculptor\Agent\Actions\Domains\Stages\Services;
use Sculptor\Agent\Actions\Domains\Stages\Structure;
use Sculptor\Agent\Actions\Domains\Stages\WebServer;
use Sculptor\Agent\Actions\Domains\Stages\Worker;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Actions\Domains\Support\Stage;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Delete extends Stage
{
    use Logging;

    protected array $stages = [
        Crontab::class,
        Worker::class,
        WebServer::class,
        Logrotate::class,
        Certificates::class,
        Structure::class,
        Services::class
    ];

    /**
     * @throws Exception
     */
    public function run(string $name): void
    {
        $options = [ 'name' => $name ];

        $domain = $this->domains->find($name);

        $this->info($domain);

        foreach ($this->stages as $stage) {
            $step = $this->make($stage);

            $options = $step->delete($domain, $options);
        }

        $this->domains->delete($name);
    }
}
