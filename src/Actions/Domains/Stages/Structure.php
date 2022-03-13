<?php

namespace Sculptor\Agent\Actions\Domains\Stages;

use Sculptor\Agent\Actions\Contracts\Domain as DomainInterface;
use Exception;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Structure implements DomainInterface
{
    use Logging;

    public function __construct(private Folders $folders)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function create(Domain $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'create');

        $template = $domain->template;

        foreach (
            [
                $domain->configs(),
                $domain->logs(),
                $domain->certs(),
                $domain->shared(),
                $domain->root(),
                $domain->bin(),
            ] as $folder
        ) {
            $this->debug($domain, $options, "make recursive $folder");

            Filesystem::makeDirectoryRecursive($folder);
        }

        Filesystem::fromTemplateDirectory($template, $domain->configs(), $this->folders->templates());

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function delete(Domain $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'delete');

        foreach (
            [
                $domain->configs(),
                $domain->logs(),
                $domain->certs(),
                $domain->shared(),
                $domain->root(),
             ] as $folder
        ) {
            $this->debug($domain, $options, "delete $folder");

            Filesystem::deleteDirectoryIfExists($folder);
        }

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function prepare(Domain $domain, array $options = null): array
    {
        throw new Exception("Apply not implemented in structure");
    }

    /**
     * @throws Exception
     */
    public function deploy(Domain $domain, array $options = null): array
    {
        throw new Exception("Deploy not implemented in structure");
    }
}
