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
class Logrotate implements DomainInterface
{
    use Logging;

    public function __construct(private Compiler $compiler, private Folders $folders)
    {
    }

    public function create(Entity $domain, array $options = null): array
    {
        // TODO: Implement create() method.

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function delete(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'delete');

        Filesystem::deleteIfExists("{$this->folders->etc()}/logrotate.d/{$domain->name()}.conf");

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function prepare(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'prepare');

        $template = Filesystem::get($domain->configs('logrotate.conf'));

        $filename = $domain->root("{$domain->name()}.logrotate.conf");

        $compiled = $this->compiler
            ->domain($template, $domain)
            ->replaces([
                '{RETAIN}' => 30 // $domain->logrotate
            ])
            ->value();

        $this->compiler
            ->save($filename, $compiled);

        Filesystem::link($filename, "{$this->folders->etc()}/logrotate.d/{$domain->name()}.conf");

        return $options ?? [];
    }

    public function deploy(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'deploy');

        return $options ?? [];
    }
}
