<?php

namespace Sculptor\Agent\Actions\Backups\Archives;

use Exception;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Sculptor\Agent\Actions\Backups\Archives\Support\ArchiveFile;
use Sculptor\Agent\Actions\Backups\Contracts\Archive;
use Sculptor\Agent\Configuration;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;


/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Dropbox implements Archive
{
    private Filesystem $filesystem;

    private string $path;

    public function __construct(Configuration $configuration)
    {
        $client = new Client($configuration->get('backup.drivers.dropbox.key'));

        $adapter = new DropboxAdapter($client);

        $this->filesystem = new Filesystem($adapter, [$configuration->getBool('backup.drivers.dropbox.case_sensitive') => false]);
    }

    public function create(string $path): Archive
    {
        $this->path = $path;

        return $this;
    }

    public function put(string $file, $content): Archive
    {
        if (!$this->filesystem->has($this->path)) {
            $this->filesystem->createDir($this->path);
        }

        $this->filesystem->put("{$this->path}/{$file}", $content);

        return $this;
    }

    /**
     * @throws FileNotFoundException
     */
    public function get(string $file)
    {
        return $this->filesystem->read("{$this->path}/{$file}");
    }

    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function delete(string $file): Archive
    {
        if (!$this->filesystem->delete("{$this->path}/{$file}")) {
            throw new Exception("Cannot delete file {$this->path}/{$file}");
        }

        return $this;
    }

    public function list(string $file): array
    {
        return collect($this->filesystem->listContents("{$this->path}/{$file}", true))
            ->map(fn($item) => new ArchiveFile($item))
            ->toArray();
    }

    public function has(string $file): bool
    {
        return $this->filesystem->has("{$this->path}/{$file}");
    }

    public function name(): string
    {
        return 'dropbox';
    }
}
