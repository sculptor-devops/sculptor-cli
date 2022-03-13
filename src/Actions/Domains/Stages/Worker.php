<?php

namespace Sculptor\Agent\Actions\Domains\Stages;

use Exception;
use Sculptor\Agent\Actions\Contracts\Domain as DomainInterface;
use Sculptor\Agent\Actions\Domains\Support\Compiler;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Entities\Domain as Entity;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Worker implements DomainInterface
{
    use Logging;

    public function __construct(private Compiler $compiler, private Folders $folders)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function create(Entity $domain, array $options = null): array
    {
        throw new Exception("Create not implemented in web server stage");
    }

    /**
     * @throws Exception
     */
    public function delete(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'delete');

        Filesystem::deleteIfExists("{$this->folders->etc()}/supervisor/conf.d/{$domain->name()}.conf");

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function prepare(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'prepare');

        $workers = $domain->workersArray;

        if (!$workers) {
            return $options ?? [];
        }

        foreach ($workers as $item) {
            $filename = 'worker.conf';

            if (array_key_exists('template', $item)) {
                $filename = $item['template'];
            }

            $template = Filesystem::get($domain->configs($filename));

            $filename = $domain->root("worker-{$item['prefix']}-{$domain->name()}.conf");

            $compiled = $this->compiler
                    ->domain($template, $domain)
                    ->replaces([
                        '{COMMAND}' => $item['command'],
                        '{PREFIX}' => $item['prefix'],
                        '{COUNT}' => $item['count'],
                    ])
                    ->value();

            $compiled = $this->compiler
                ->domain($compiled, $domain)
                ->value();

            $this->compiler
                ->save($filename, $compiled);

            $this->debug($domain, $options, "linking $filename");

            Filesystem::link($filename, "{$this->folders->etc()}/supervisor/conf.d/{$domain->name()}.conf");
        }

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function deploy(Entity $domain, array $options = null): array
    {
        throw new Exception("Deploy not implemented in web server stage");
    }
}
