<?php

namespace Sculptor\Agent\Actions\Domains\Support;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Databases;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Actions\Contracts\Domain as DomainInterface;
use Sculptor\Agent\Support\Folders;
use Sculptor\Agent\Support\Version\Php;
use Sculptor\Foundation\Support\Replacer;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Stage
{
    protected array $stages = [];
    protected Configuration $configuration;
    protected Folders $folders;
    protected Domains $domains;
    protected Php $php;
    protected Databases $databases;

    public function __construct(Configuration $configuration, Folders $folders, Domains $domains, Databases $databases, Php $php)
    {
        $this->configuration = $configuration;

        $this->folders = $folders;

        $this->domains = $domains;

        $this->databases = $databases;

        $this->php = $php;
    }


    /**
     * @throws Exception
     */
    protected function compile(Domain $domain, string $template): Replacer
    {
        return Replacer::make($template)
            ->replace('{DOMAINS}', $domain->serverNames())
            ->replace('{URL}', "https://{$domain->name()}")
            ->replace('{NAME}', $domain->name())
            ->replace('{PATH}', $domain->root())
            ->replace('{PUBLIC}', $domain->public())
            ->replace('{CURRENT}', $domain->current())
            ->replace('{USER}', $domain->user)
            ->replace('{PHP}', $this->php->path($domain->engine ?? ENGINE_VERSION))
            ->replace('{PHP_VERSION}', $domain->engine);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function make(string $stage): DomainInterface
    {
        return app()->make($stage);
    }
}
