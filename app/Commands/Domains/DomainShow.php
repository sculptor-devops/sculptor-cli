<?php

namespace App\Commands\Domains;

use Exception;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainShow extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:show {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show domains or single domain parameters';

    /**
     * Execute the console command.
     *
     * @param Domains $domains
     * @return int
     * @throws Exception
     */
    public function handle(Domains $domains): int
    {
        $name = $this->argument('name');

        if ($name) {
            $this->showSingle($domains->find($name));

            return 0;
        }

        $this->showAll($domains->all());

        return 0;
    }

    /**
     * @throws Exception
     */
    private function showSingle(Domain $domain): void
    {
        $values = [
            ['name' => 'enabled', 'value' => $this->yesNo($domain->enabled)],
            [ 'name' => '<<readonly>>', 'value' => $domain->name()],
            ['name' => 'www', 'value' => $this->yesNo($domain->www)],
            ['name' => 'aliases', 'value' => $this->empty($domain->aliases)],
            ['name' => 'template', 'value' => $domain->template],
            ['name' => '<<readonly>>', 'value' => $domain->status],

            ['name' => 'certificate.type', 'value' => $domain->certificateType],
            ['name' => 'certificate.email', 'value' => $this->empty($domain->certificateEmail)],

            ['name' => 'user', 'value' => $domain->user],
            ['name' => 'engine', 'value' => $domain->engine],
            ['name' => '<<readonly>>', 'value' => $domain->root()],

            ['name' => 'database.name', 'value' => $this->empty($domain->databaseName)],
            ['name' => 'database.user', 'value' => $this->empty($domain->databaseUser)],

            ['name' => 'deploy.command', 'value' => $domain->deployCommand],
            ['name' => 'deploy.install', 'value' => $domain->deployInstall],
            ['name' => 'webhook.url', 'value' => $this->empty($domain->webhook())],

            ['name' => 'git.url', 'value' => $this->empty($domain->gitUrl)],
            ['name' => 'git.branch', 'value' => $this->empty($domain->gitBranch)],

            ['name' => '<<specific command>>', 'value' => count($domain->crontabArray)],
            ['name' => '<<specific command>>', 'value' => count($domain->workersArray)],

            ['name' => '<<readonly>>', 'value' => $this->yesNo($domain->validate())],
            ['name' => '<<readonly>>', 'value' => $this->empty($domain->created)],
            ['name' => '<<readonly>>', 'value' => $this->empty($domain->deployed)]
        ];

        $this->table(['Name', 'Value'], $values);

        $this->warn("Name column indicate the key to use when change value with setup command.");
        $this->warn("Example: use domain:setup {$domain->name()} <<key>> <<value>>");
    }

    private function showAll(array $all): void
    {
        $tabled = [];

        $count = count($all);

        foreach ($all as $domain) {
            $tabled[] = [
                $this->yesNo($domain->enabled),
                $domain->name(),
                $this->empty($domain->aliases),
                $domain->template,
                $domain->status,
                $domain->user,
                $domain->engine,
                $this->empty($domain->databaseName) . ' (user ' . $this->empty($domain->databaseUser) . ')'
            ];
        }

        $this->table(['Enabled', 'Name', 'Aliases', 'Template', 'Status', 'User', 'Engine', 'Database'], $tabled);

        $this->info("{$count} domains");
    }
}
