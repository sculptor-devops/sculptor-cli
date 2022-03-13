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
class Workers extends Stage
{
    use Logging;

    /**
     * @throws Exception
     */
    public function run(string $name, string $prefix, string $command, int $count): void
    {
        $domain = $this->domains->find($name);

        $this->info($domain, ['prefix' => $prefix, 'method' => 'add']);

        $domain->workersArray = Items::make($domain->workersArray)
            ->add([ 'prefix' => $prefix, 'command' => $command, 'count' => $count ])
            ->toArray();

        $domain->save();
    }

    /**
     * @throws Exception
     */
    public function delete(string $name, int $index): void
    {
        $domain = $this->domains->find($name);

        $this->info($domain, ['prefix' => $index, 'method' => 'delete']);

        $domain->workersArray = items::make($domain->workersArray)
            ->delete($index)
            ->toArray();

        $domain->save();
    }

    /**
     * @throws Exception
     */
    public function all(string $name): array
    {
        $domain = $this->domains->find($name);

        return Items::make($domain->workersArray)->all();
    }
}
