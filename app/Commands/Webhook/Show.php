<?php


namespace App\Commands\Webhook;


use Exception;
use Sculptor\Agent\Actions\Webhook\Support\Repository;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Show extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show webhook site properties';

    /**
     * @throws Exception
     */
    public function handle(Configuration $configuration, Repository $deploys, Domains $domains): int
    {
        if (!$configuration->get('webhook')) {
            $this->warn('No webhook domain defined');

            return 1;
        }

        $all = $deploys->all();

        $this->table(['Token', 'Status', 'Task', 'Branch', 'Updated', 'Url'], collect($all)->map(function ($item) use ($domains) {
            return [
              'token' => $item->token,
              'status' => $item->status,
              'task' => $this->empty($item->task),
              'branch' => $item->branch,
              'updated' => $item->updated_at,
                'url' => $domains->findByToken($item->token)->webhook()
            ];
        }));

        return 0;
    }
}
