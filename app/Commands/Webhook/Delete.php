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
class Delete extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete webhook site';

    /**
     * @throws Exception
     */
    public function handle(Configuration $configuration): int
    {
        if (!$configuration->get('webhook')) {
            $this->warn('No webhook domain defined');

            return 1;
        }

        $name = $configuration->get('webhook');

        $configuration->save([ 'webhook' => '' ]);

        $this->call('domain:delete', ['name' => $name ]);

        return 0;
    }
}
