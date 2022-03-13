<?php

namespace App\Commands\Webhook;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Create extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a webhook site';

    /**
     * @throws Exception
     */
    public function handle(Configuration $configuration): int
    {
        $name = $this->argument('name');

        $configuration->save(['webhook' => $name]);

        foreach ([
                     'domain:create' => ['name' => $name],
                     'domain:setup' => ['name' => $name, 'key' => 'git.url', 'value' => 'https://github.com/sculptor-devops/webhook'],
                     'domain:env' => ['name' => $name, 'key' => 'DB_CONNECTION', 'value' => 'sqlite'],
                     'domain:deploy' => ['name' => $name],
                     'webhook:check' => [],
                     'webhook:show' => []
                 ] as $command => $arguments) {
            $this->call($command, $arguments);
        }

        return 0;
    }
}
