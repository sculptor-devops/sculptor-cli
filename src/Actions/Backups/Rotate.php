<?php

namespace Sculptor\Agent\Actions\Backups;

use Exception;
use Sculptor\Agent\Actions\Backups\Factories\Archives;
use Sculptor\Agent\Actions\Backups\Factories\Compressions;
use Sculptor\Agent\Actions\Backups\Factories\Rotations;
use Sculptor\Agent\Actions\Backups\Support\Tag;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Logs\Facades\Logs;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Support\Chronometer;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Rotate
{
    use Logging;

    public function __construct(private Backups $backups, private Rotations $rotations, private Archives $archives, private Compressions $compression)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name, bool $dry = false): int
    {
        $result = 0;

        $backup = $this->backups->find($name);

        $archive = $this->archives->make($backup->archiveDriver);

        $rotation = $this->rotations->make($backup->rotationPolicy);

        $compression = $this->compression->make($backup->compression);

        $this->debug($backup, [], "rotating {$archive->name()}, {$compression->name()}, {$rotation->name()}");

        $timer = Chronometer::start();

        try {
            $tag = Tag::make($backup)->extension($compression->extension());

            $current = $archive->create($tag->archive())->list('/');

            $needRotation = $rotation->rotate($current, $backup->rotationCount);

            foreach ($needRotation as $file) {
                $this->debug($backup, [], "Rotating file {$file->name()}");

                if (!$dry) {
                    $archive->delete($file->name());
                }

                return $result++;
            }

            $this->info($backup, [], "Rotated $result archives in {$timer->stop()}");

            return $result;
        } catch (Exception $ex) {
            report($ex);

            $backup->error("Rotate error: {$ex->getMessage()}");

            $this->err($backup, [], $ex->getMessage());

            return -1;
        }
    }
}
