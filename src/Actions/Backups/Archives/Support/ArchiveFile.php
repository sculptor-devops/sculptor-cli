<?php

namespace Sculptor\Agent\Actions\Backups\Archives\Support;

use Illuminate\Support\Arr;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class ArchiveFile
{
    /*
     *
     "type" => "file"
    "path" => ".env"
    "timestamp" => 1643205775
    "size" => 855
    "dirname" => ""
    "basename" => ".env"
    "extension" => "env"
    "filename" => ""
     */

    public function __construct(private array $data)
    {
        //
    }

    public function name(): string
    {
        return Arr::get($this->data, 'basename');
    }

    public function path(): string
    {
        return Arr::get($this->data, 'path');
    }

    public function timestamp(): int
    {
        return Arr::get($this->data, 'timestamp');
    }

    public function size(): int
    {
        return Arr::get($this->data, 'size');
    }

    public function isFile(): bool
    {
        return Arr::get($this->data, 'type') == 'file';
    }

    public function extension(): string
    {
        return Arr::get($this->data, 'extension');
    }
}
