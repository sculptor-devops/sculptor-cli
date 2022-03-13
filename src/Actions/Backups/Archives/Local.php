<?php

namespace Sculptor\Agent\Actions\Backups\Archives;

use Exception;
use Sculptor\Agent\Actions\Backups\Archives\Support\ArchiveFile;
use Sculptor\Agent\Actions\Backups\Contracts\Archive;
use Illuminate\Support\Facades\File;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Local implements Archive
{
    private Filesystem $filesystem;

    public function create(string $path): Archive
    {
        $adapter = new LocalAdapter($path);

        $this->filesystem = new Filesystem($adapter);

        return $this;
    }

    /**
     * @throws FileExistsException
     */
    public function put(string $file, $content): Archive
    {
        if (!File::exists(dirname($file))) {
            File::makeDirectory(dirname($file), 0755, true);
        }

        $this->filesystem->write($file, $content);

        return $this;
    }

    /**
     * @throws FileNotFoundException
     */
    public function get(string $file)
    {
        return $this->filesystem->read($file);
    }

    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function delete(string $file): Archive
    {
        if (!$this->filesystem->delete($file)) {
            throw new Exception("Cannot delete file {$file}");
        }

        return $this;
    }

    public function list(string $file): array
    {
        return collect($this->filesystem->listContents($file, true))
            ->map(fn($item) => new ArchiveFile($item))
            ->toArray();
    }

    public function has(string $file): bool
    {
        return $this->filesystem->has($file);
    }

    public function name(): string
    {
        return 'local';
    }
}
