<?php

namespace Sculptor\Agent\Actions\Domains\Stages;

use Exception;
use Illuminate\Support\Arr;
use Sculptor\Agent\Actions\Contracts\Domain as DomainInterface;
use Sculptor\Agent\Actions\Domains\Support\Compiler;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Entities\Domain as Entity;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Sculptor\Agent\Support\System;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Deploy implements DomainInterface
{
    use Logging;

    public function __construct(private Compiler $compiler, private System $system, private Folders $folders)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function create(Entity $domain, array $options = null): array
    {
        throw new Exception("Create not implemented in deploy stage");
    }

    /**
     * @throws Exception
     */
    public function delete(Entity $domain, array $options = null): array
    {
        throw new Exception("Delete not implemented in deploy stage");
    }

    /**
     * @throws Exception
     */
    public function prepare(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'prepare');

        $template = Filesystem::get($domain->configs('deployer.php'));

        $compiled = $this->compiler
            ->domain($template, $domain)
            ->value();

        $this->compiler
            ->save($domain->root('deploy.php'), $compiled);

        if (!Filesystem::exists($domain->public())) {
            $this->debug($domain, $options, 'symlink');

            foreach ([ 'deploy:prepare', 'deploy:release', 'deploy:symlink' ] as $stage) {
                $this->run($stage, $domain);
            }

            Filesystem::makeDirectoryRecursive($domain->public());

            Filesystem::fromTemplateFile('index.html', $domain->public('index.html'), $this->folders->templates());
        }

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function deploy(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'deploy');

        $command = Arr::get($options, 'task', 'deploy');

        $force = Arr::get($options, 'force', false);

        if (!$domain->gitUrl || !$domain->gitBranch) {
            throw new Exception("Repository or branch not set");
        }

        if ($force) {
            $this->debug($domain, $options, 'unlock');

            foreach (['deploy:unlock', $command] as $stage) {
                $this->run($stage, $domain);
            }
        }

        $this->run($command, $domain);

        return $options ?? [];
    }

    /**
     * @param string $command
     * @param Entity $domain
     * @return void
     * @throws Exception
     */
    private function run(string $command, Entity $domain): void
    {
        $this->info($domain, [], "deploy $command");

        $this->system
            ->runAs(
                $domain->root(),
                $domain->user,
                [
                    $domain->bin('dep'),
                    $command,
                    '--log',
                    $domain->logs('deploy.log')
                ],
                null,
                function ($type, $buffer) use ($domain) {
                    $this->debug($domain, [], "deploy: $buffer");
                }
            );
    }
}
