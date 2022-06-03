<?php

namespace Sculptor\Agent\Actions\Backups;

use Carbon\Carbon;
use Cron\CronExpression;
use Exception;
use Sculptor\Agent\Actions\Backups\Factories\Archives;
use Sculptor\Agent\Actions\Backups\Factories\Compressions;
use Sculptor\Agent\Actions\Backups\Support\Tag;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Backups;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Check
{
    use Logging;

    private string $error;

    public function __construct(private Backups $backups, private Archives $archives, private Compressions $compression)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name): bool
    {
        $backup = $this->backups->find($name);

        $this->info($backup, [], 'check');

        $compression = $this->compression->make($backup->compression);

        $tag = Tag::make($backup)->extension($compression->extension());

        $archive = $this->archives->make($backup->archiveDriver)->create($tag->archive());

        $recent = collect($archive->list('/'))
            ->filter(fn($file) => $tag->match($file->name()))
            ->sortBy('timestamp')
            ->last();

        if (!$recent) {
            $this->error = 'No archive found';

            $this->err($backup, [], $this->error);

            return false;
        }

        if ($recent->size() == 0) {
            $this->error = "Last backup {$recent->name()} size is zero";

            $this->err($backup, [], $this->error);

            return false;
        }

        $timestamp = Carbon::parse($recent->timestamp());

        $cron = new CronExpression($backup->cron);

        $current = now()->diffInMinutes($timestamp);

        $last = now()->diffInMinutes($cron->getPreviousRunDate());

        if ($current > $last) {
            $last = Carbon::parse($cron->getPreviousRunDate());

            $this->error = "Last backup was {$timestamp} and was scheduled to be $last";

            $this->err($backup, [], $this->error);

            return false;
        }

        $this->info($backup, [], 'backup ok');

        return true;
    }

    public function error(): string
    {
        return $this->error;
    }
}
