<?php

namespace App\Commands\Webhook;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Sculptor\Agent\Actions\Webhook\Deploy as DeployAction;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Actions\Webhook\Update as UpdateAction;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Check extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check webhook deploys';

    /**
     * @throws Exception
     */
    public function handle(Configuration $configuration, UpdateAction $update, DeployAction $deploy): int
    {
        if (!$configuration->get('webhook')) {
            $this->warn('No webhook domain defined');

            return 1;
        }

        $updated = $update->run();

        $deploys = $deploy->run();

        $this->info('Updated ' . $this->glue($updated));

        $this->info('Deployed ' . $this->glue($deploys));

        return 0;
    }

    private function glue(array $items): string
    {
        $glued = collect($items)->map(fn($item) => $item->name())->join(', ');

        return $this->empty($glued);
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->everyMinute()->withoutOverlapping();
    }
}
