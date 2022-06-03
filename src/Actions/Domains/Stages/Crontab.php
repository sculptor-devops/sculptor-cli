<?php

namespace Sculptor\Agent\Actions\Domains\Stages;

use Exception;
use Illuminate\Support\Arr;
use Sculptor\Agent\Actions\Domains\Support\Compiler;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Repositories\Entities\Domain as Entity;
use Sculptor\Agent\Actions\Contracts\Domain as DomainInterface;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\System;

use function React\Promise\reject;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Crontab implements DomainInterface
{
    use Logging;

    public function __construct(private Configuration $configuration, private Compiler $compiler, private System $system, private Domains $domains)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function create(Entity $domain, array $options = null): array
    {
        throw new Exception("Create not implemented in crontab stage");
    }

    /**
     * @throws Exception
     */
    public function delete(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'delete');

        $this->update($domain, true);

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function prepare(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'prepare');

        $this->update($domain);

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function deploy(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'deploy');

        $this->update($domain);

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    private function update(Entity $domain, bool $delete = false): void
    {
        $compiled = '';

        $domains = collect($this->domains->all())
            ->filter(fn(Entity $item) => $item->user == $domain->user)
            ->reject(fn(Entity $item) => $delete && $item->name() == $domain->name());

        $root = $this->configuration->root();

        $filename = "/tmp/.sculptor.crontab.{$domain->user}.conf";

        foreach ($domains as $item) {
            $compiled .= $this->crontab($item);
        }

        if (!$compiled) {
            $compiled = "# No crontab for {$domain->name()}";
        }

        $this->compiler
            ->save($filename, $compiled);

        $this->system
            ->run($root, ['crontab', '-u', $domain->user, $filename]);
    }

    /**
     * @throws Exception
     */
    public function crontab(Entity $domain): string
    {
        $compiled = '';

        $crontab = $domain->crontabArray;

        if (!$crontab) {
            return '';
        }

        $this->debug($domain, [], "crontab for {$domain->name()}");

        foreach ($crontab as $item) {
            $schedule = $item['schedule'];

            $command =  $item['command'];

            $compiled = $compiled . $this->compiler
                    ->domain("{$schedule} {$command}", $domain)
                    ->value();

            $compiled = $this->compiler
                ->domain($compiled, $domain)
                ->value();
        }

        return "# {$domain->name()}\n{$compiled}\n";
    }
}
