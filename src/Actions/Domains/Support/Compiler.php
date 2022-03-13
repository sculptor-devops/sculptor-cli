<?php

namespace Sculptor\Agent\Actions\Domains\Support;

use Exception;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Version\Php;
use Sculptor\Foundation\Support\Replacer;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Compiler
{
    public function __construct(private Php $php)
    {
        //
    }

    /**
     * @param string $filename
     * @param string $type
     * @return string
     */
    public function load(string $filename, string $type): string
    {
        if (!Filesystem::exists($filename)) {
            $templates = base_path("templates/{$type}");

            Filesystem::copy("{$templates}/{$filename}", $filename);
        }

        return Filesystem::get($filename);
    }

    /**
     * @throws Exception
     */
    public function domain(string $template, Domain $domain): Replacer
    {
        return Replacer::make($template)
            ->replace('{DOMAINS}', $domain->serverNames())
            ->replace('{URL}', "https://{$domain->name()}")
            ->replace('{NAME}', $domain->name())
            ->replace('{PATH}', $domain->root())
            ->replace('{PUBLIC}', $domain->public())
            ->replace('{BIN}', $domain->bin())
            ->replace('{CURRENT}', $domain->current())
            ->replace('{USER}', $domain->user)
            ->replace('{REPOSITORY}', $domain->gitUrl)
            ->replace('{BRANCH}', $domain->gitBranch)
            ->replace('{PHP}', $this->php->path($domain->engine ?? ENGINE_VERSION))
            ->replace('{PHP_VERSION}', $domain->engine);
    }

    /**
     * @param string $filename
     * @param string $compiled
     * @return bool
     * @throws Exception
     */
    public function save(string $filename, string $compiled): bool
    {
        if (!Filesystem::put($filename, $compiled)) {
            throw new Exception("Cannot create file {$filename}");
        }

        return true;
    }
}
