<?php

namespace Sculptor\Agent\Actions\Backups\Archives;

use Aws\S3\S3Client;
use Exception;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Sculptor\Agent\Actions\Backups\Archives\Support\ArchiveFile;
use Sculptor\Agent\Actions\Backups\Contracts\Archive;
use Sculptor\Agent\Configuration;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class S3 implements Archive
{
    private Filesystem $filesystem;

    private string $path;

    public function __construct(Configuration $configuration)
    {
        $client = new S3Client([
            'credentials' => [
                'key'    => $configuration->get('backup.drivers.s3.key'),
                'secret' => $configuration->get('backup.drivers.s3.secret'),
            ],
            'version' => 'latest',
            'region' => $configuration->get('backup.drivers.s3.region'),
            'endpoint' => $configuration->get('backup.drivers.s3.endpoint')
        ]);

        $adapter = new AwsS3Adapter($client, $configuration->get('backup.drivers.s3.bucket'));

        $this->filesystem = new Filesystem($adapter);
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

    /**
     * @throws FileNotFoundException
     */
    public function get(string $file)
    {
        return $this->filesystem->read("{$this->path}/{$file}");
    }

    public function has(string $file): bool
    {
        return $this->filesystem->has("{$this->path}/{$file}");
    }

    public function name(): string
    {
        return 's3';
    }
}
