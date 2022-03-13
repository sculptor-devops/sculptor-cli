<?php

namespace Sculptor\Agent\Support\Version;

use Sculptor\Agent\Configuration;
use Illuminate\Support\Facades\File;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Php
{
    private array $versions = [ '5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0', '8.1' ];

    public function path(string $version = ''): string
    {
        return ENGINE_PATH . $version;
    }

    public function all(): array
    {
        return $this->versions;
    }

    public function available(): array
    {
        $versions = [];

        foreach ($this->versions as $version) {
            if ($this->installed($version)) {
                $versions[] = $version;
            }
        }

        return $versions;
    }

    public function installed(string $version): bool
    {
        return File::exists($this->path($version));
    }
}
