<?php


namespace Sculptor\Agent\Support\Version;


use Sculptor\Agent\Support\Filesystem;
use Sculptor\Foundation\Contracts\Runner;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Node
{
    public const PATH = '/usr/bin/node';

    public function __construct(private Runner $runner)
    {
        //
    }

    public function path(): string
    {
        return static::PATH;
    }

    public function available(): array
    {
        return [ $this->version() ];
    }

    public function version(): ?string
    {
        if (!Filesystem::exists($this->path())) {
            return null;
        }

        $version = $this->runner->runOrFail([$this->path(), '-v']);

        return str_replace([ "\r", "\n" ], '', $version) ;
    }

    public function installed(string $version): bool
    {
        return $version == $this->version();
    }
}
