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
        $values = $this->formatRowsShown([
            ['enabled', $domain->enabled, 'yesNo', false],
            ['name', $domain->name(), '', true],
            ['www', $domain->www, 'yesNo', false],
            ['aliases', $domain->aliases, 'empty', false],
            ['template', $domain->template, '', false],
            ['status', $domain->status, '', true],

            ['certificate.type', $domain->certificateEmail, 'empty', false],
            ['certificate.type', $domain->certificateType, 'empty', false],

            ['user', $domain->user, '', false],
            ['engine', $domain->engine, '', false],
            ['root', $domain->root(), '', true],

            ['database.name', $domain->databaseName, 'noYes', false],
            ['database.user', $domain->databaseUser, 'noYes', false],

            ['deploy.command', $domain->deployCommand, '', false],
            ['deploy.install', $domain->deployInstall, '', false],
            ['webhook.url', $domain->webhook(), 'empty', true],

            ['git.url', $domain->gitUrl, 'empty', false],
            ['git.branch', $domain->gitBranch, 'empty', true],

            ['valid', $domain->validate(), 'yesNo', true],
            ['created', $domain->created, 'empty', true],
            ['deployed', $domain->deployed, 'empty', true],
        ]);

        $this->table(['Name', 'Value', 'Readonly'], $values);

        $this->warn("Name column indicate the key to use when change value with setup command.");
        $this->warn("Example: use domain:setup {$domain->name()} <<key>> <<value>>");
        $this->warn("Execute command: {$domain->bin('php')} SOME_COMMAND");
        $this->warn("Execute composer: {$domain->bin('composer')} SOME_COMMAND");
        $this->warn("Execute deploy: {$domain->bin('dep')} SOME_COMMAND");
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
