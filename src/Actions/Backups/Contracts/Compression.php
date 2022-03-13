<?php

namespace Sculptor\Agent\Actions\Backups\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Compression
{
    public function name(): string;

    public function create(string $filename): Compression;

    public function close(): void;

    public function directory(string $name, string $path = null): Compression;

    public function file(string $file): Compression;

    public function extension(): string;
}
