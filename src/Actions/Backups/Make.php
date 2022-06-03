<?php

namespace Sculptor\Agent\Actions\Backups;

use Exception;
use InvalidArgumentException;
use Sculptor\Agent\Actions\Backups\Contracts\Compression;
use Sculptor\Agent\Actions\Backups\Factories\Archives;
use Sculptor\Agent\Actions\Backups\Factories\Compressions;
use Sculptor\Agent\Actions\Backups\Factories\Strategies;
use Sculptor\Agent\Actions\Backups\Support\Metadata;
use Sculptor\Agent\Actions\Backups\Support\Tag;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Support\Chronometer;
use Sculptor\Agent\Support\Filesystem;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Make
{
    use Logging;

    public function __construct(private Backups $backups, private Strategies $strategies, private Archives $archives, private Compressions $compression, private Metadata $meta)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name): bool
    {
        $timer = Chronometer::start();

        $backup = $this->backups->find($name);

        $backup->running();

        $strategy = $this->strategies->make($backup->resource);

        $archive = $this->archives->make($backup->archiveDriver);

        $compression = $this->compression->make($backup->compression);

        $tag = Tag::make($backup)->extension($compression->extension());

        $files = $strategy->create($backup->target);

        $this->info($backup, [], "running  using {$strategy->name()}, {$archive->name()}, {$compression->name()}");

        try {
            $this->writeMeta($backup, $strategy->meta($backup->target), $files, $tag->meta());

            $this->compress($backup, $files, $compression, $tag->compressedTemp(), $tag->meta());

            $this->debug($backup, [], "archive {$backup->archivePath}/{$tag->archive()}");

            $archive->create($tag->archive())
                ->put($tag->compressed(), Filesystem::get($tag->compressedTemp()));

            $this->debug($backup, [], "done: " . byteToHumanReadable(Filesystem::size($tag->compressedTemp())) . " in {$timer->stop()}");

            Filesystem::delete($tag->compressedTemp());

            $backup->success();

            return true;
        } catch (Exception $ex) {
            report($ex);

            $backup->error("Backup error: {$ex->getMessage()}");

            $this->err($backup, [], $ex->getMessage());

            return false;
        }
    }

    /**
     * @throws Exception
     */
    private function writeMeta(Backup $backup, array $configuration, array $files, string $filename): void
    {
        $checksum = [];

        foreach ($files as $root => $path) {
            $checksum = $checksum + $this->checksumDir($root, $backup->checksum);
        }

        $this->meta->make($filename, [
            'directories' => $files,
            'configuration' => $configuration,
            'checksum' => [
                'algo' => $backup->checksum,
                'files' => $checksum
            ]
        ]);
    }

    private function checksumDir(string $path, string $algo): array
    {
        if (!$algo) {
            return [];
        }

        $result = [];

        foreach (Filesystem::allFiles($path, true) as $file) {
            $result[$file->getPathname()] = match ($algo) {
                'sha1' => sha1_file($file->getPathname()),
                'md5' => md5_file($file->getPathname()),
                default => throw new InvalidArgumentException("Invalid checksum algorithm $algo")
            };
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function compress(Backup $backup, array $files, Compression $compression, string $filename, string $metadata): void
    {
        $compressor = $compression->create($filename);

        foreach ($files as $root => $path) {
            $this->debug($backup, [], "compression  $root > $filename");

            $compressor->directory($root, $path);
        }

        $compression->file($metadata);

        $compressor->close();
    }
}
