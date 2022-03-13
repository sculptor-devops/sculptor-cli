<?php

namespace Sculptor\Agent\Actions\Backups\Support;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Support\Chronometer;
use Sculptor\Agent\Support\Filesystem;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Tag
{
    private string $timestamp;

    private string $extension;

    /**
     * @throws Exception
     */
    public function __construct(private Backup $backup)
    {
        $this->timestamp = Chronometer::tag();

        Filesystem::makeDirectoryRecursive($this->temp());
    }

    /**
     * @throws Exception
     */
    public static function make(Backup $backup): Tag
    {
        return new Tag($backup);
    }

    public function extension(string $extension): Tag
    {
        $this->extension = $extension;

        return $this;
    }

    public function prefix(): string
    {
        return "{$this->backup->resource}-{$this->backup->target}-";
    }

    public function name(): string
    {
        return "{$this->prefix()}{$this->timestamp}";
    }

    public function compressed(): string
    {
        return "{$this->name()}.{$this->extension}";
    }

    public function compressedTemp(): string
    {
        return "{$this->backup->temp}/{$this->compressed()}";
    }

    public function temp(): string
    {
        return $this->backup->temp;
    }

    public function archive(): string
    {
        return "{$this->backup->archivePath}/{$this->backup->name()}";
    }

    public function match(string $compare): bool
    {
        return Str::startsWith($compare, $this->prefix()) &&
               Str::endsWith($compare, $this->extension);
    }

    public function meta(): string
    {
        return "{$this->temp()}/{$this->backup->name()}-metadata.yml";
    }
}
