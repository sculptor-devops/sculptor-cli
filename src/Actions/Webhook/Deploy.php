<?php

namespace Sculptor\Agent\Actions\Webhook;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Sculptor\Agent\Actions\Webhook\Support\Repository;
use Sculptor\Agent\Logs\Facades\Logs;
use Sculptor\Agent\Repositories\Domains;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Deploy
{
    public function __construct(private Repository $webhook, private Domains $domains)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function __invoke(): array
    {
        $result = [];

        foreach ($this->webhook->pending() as $deploy) {
            $domain = $this->domains->findByToken($deploy->token);

            $repository = Str::of($deploy->repository)->replace('https://', '');

            if (!Str::of($domain->gitUrl)->contains($repository)) {
                Logs::actions(['name' => $domain->name() ])->warning("Webhook invalid repository: {$deploy->message} ({$deploy->commit})");

                $this->webhook->message($domain, 'invalid_repository');

                continue;
            }

            if ($domain->gitBranch != $deploy->branch) {
                Logs::actions(['name' => $domain->name() ])->warning("Webhook invalid branch: {$deploy->message} ({$deploy->commit})");

                $this->webhook->message($domain, 'invalid_branch');

                continue;
            }

            Logs::actions(['name' => $domain->name() ])->info("Webhook received: {$deploy->message} ({$deploy->commit})");

            Artisan::call('domain:deploy', ['name' => $domain->name(), 'task' => $deploy->task]);

            $this->webhook->status($domain);
        }

        return $result;
    }
}
