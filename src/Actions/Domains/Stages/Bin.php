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
class Bin implements DomainInterface
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
        throw new Exception("Create not implemented in composer stage");
    }

    /**
     * @throws Exception
     */
    public function delete(Entity $domain, array $options = null): array
    {
        throw new Exception("Delete not implemented in composer stage");
    }

    /**
     * @throws Exception
     */
    public function prepare(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'prepare');

        foreach ([
                     'composer' => "{PHP} /usr/bin/composer $@",
                     'dep' => "{PHP} /usr/local/bin/dep $@",
                     'php' => "{PHP} $@"
                 ] as $name => $content) {
            $filename = $domain->bin($name);

            $compiled = $this->compiler
                ->domain($content, $domain)
                ->value();

            $this->compiler
                ->save($filename, $compiled);

            $this->system->chmod($filename, 755);
        }

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function deploy(Entity $domain, array $options = null): array
    {
        throw new Exception("Deploy not implemented in composer stage");
    }
}
