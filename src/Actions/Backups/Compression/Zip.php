<?php

namespace Sculptor\Agent\Actions\Backups\Compression;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Sculptor\Agent\Actions\Backups\Archives\Local;
use Sculptor\Agent\Actions\Backups\Contracts\Compression;


/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Zip implements Compression
{
    private string $filename;

    private Filesystem $filesystem;

    public function create(string $filename): Compression
    {
        $this->filename = $filename;

        $this->open();

        return $this;
    }

    private function open(): void
    {
        $adapter = new ZipArchiveAdapter($this->filename);

        $this->filesystem = new Filesystem($adapter);
    }

    public function close(): void
    {
        $this->filesystem
            ->getAdapter()
            ->getArchive()
            ->close();
    }

    /**
     * @throws FileNotFoundException
     */
    public function directory(string $name, string $path = null): Compression
    {
        $local = new Local();

        $local->create($name);

        if ($path == null) {
            $path = $name;
        }

        foreach ($local->list('/') as $file) {
            if ($file->isFile()) {
                $content = $local->get($file->path());

                $this->filesystem
                    ->put("{$path}/{$file->path()}", $content);
            }
        }

        return $this;
    }

    /**
     * @throws FileNotFoundException
     */
    public function file(string $file): Compression
    {
        $local = new Local();

        $local->create(dirname($file));

        $content = $local->get(basename($file));

        $this->filesystem
            ->put(basename($file), $content);

        return $this;
    }

    public function extension(): string
    {
        return 'zip';
    }

    public function name(): string
    {
        return 'zip';
    }
}
