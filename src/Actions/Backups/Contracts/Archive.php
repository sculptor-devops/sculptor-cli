<?php

namespace Sculptor\Agent\Actions\Backups\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Archive
{
    public function name(): string;

    public function create(string $path): Archive;

    public function put(string $file, $content): Archive;

    public function get(string $file);

    public function delete(string $file): Archive;

    public function list(string $file): array;

    public function has(string $file): bool;
}
