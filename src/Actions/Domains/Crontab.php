<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Support\Items;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Actions\Domains\Support\Stage;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Crontab extends Stage
{
    use Logging;

    /**
     * @throws Exception
     */
    public function run(string $name, string $schedule, string $command): void
    {
        $domain = $this->domains->find($name);

        $this->info($domain);

        $domain->crontabArray = Items::make($domain->crontabArray)
            ->add([ 'schedule' => $schedule, 'command' => $command ])
            ->toArray();

        $domain->save();
    }

    /**
     * @throws Exception
     */
    public function all(string $name): array
    {
        $domain = $this->domains->find($name);

        return Items::make($domain->crontabArray)->all();
    }

    /**
     * @throws Exception
     */
    public function delete(string $name, int $index): void
    {
        $domain = $this->domains->find($name);

        $domain->crontabArray = items::make($domain->crontabArray)
            ->delete($index)
            ->toArray();

        $domain->save();
    }
}
