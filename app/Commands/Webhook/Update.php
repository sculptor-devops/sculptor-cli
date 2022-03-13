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
class Update extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update webhook site version';

    /**
     * @throws Exception
     */
    public function handle(Configuration $configuration): int
    {
        $name = $configuration->get('webhook');

        $this->call('domain:prepare', ['name' => $name]);

        $this->call('domain:deploy', ['name' => $name]);
        return 0;
    }
}
