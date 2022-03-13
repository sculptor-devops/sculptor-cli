<?php

namespace App\Commands\Backups;

use Exception;
use Lorisleiva\CronTranslator\CronParsingException;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Support\Command\Base;
use Lorisleiva\CronTranslator\CronTranslator;

class BackupShow extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'backup:show {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show backups';

    /**
     * Execute the console command.
     *
     * @param Backups $backups
     * @return int
     * @throws Exception
     */
    public function handle(Backups $backups): int
    {
        $name = $this->argument('name');

        if (!$name) {
            return $this->all($backups->all());
        }

        return $this->single($backups->find($name));
    }

    /**
     * @throws CronParsingException
     */
    private function all(array $all): int
    {
        $tabled = [];

        $count = count($all);

        foreach ($all as $backup) {
            $tabled[] = [
                $this->yesNo($backup->enabled),
                $backup->status,
                $backup->name(),
                $backup->resource,
                $backup->target,
                $backup->archiveDriver,
                $backup->rotationPolicy,
                CronTranslator::translate($backup->cron),
                $backup->last,
            ];
        }

        $this->table(['Enabled', 'Status', 'Name', 'Resource', 'Target', 'Archive', 'Rotation', 'Cron', 'Last run'], $tabled);

        $this->info("{$count} backups");

        return 0;
    }

    /**
     * @throws CronParsingException
     */
    private function single(Backup $backup): int
    {
        $this->table(['Name', 'Value'],
            [
                ['name' => 'enabled', 'Value' => $this->yesNo($backup->enabled)],
                ['name' => 'status', 'Value' => $backup->status],
                ['name' => 'name', 'Value' => $backup->name()],
                ['name' => 'resource', 'Value' => $backup->resource],
                ['name' => 'target', 'Value' => $backup->target],

                ['name' => 'temp', 'Value' => $backup->temp],

                ['name' => 'archive.driver', 'Value' => $backup->archiveDriver],
                ['name' => 'archive.path', 'Value' => $backup->archivePath],

                ['name' => 'compression', 'Value' => $backup->compression],
                ['name' => 'cron', 'Value' => CronTranslator::translate($backup->cron)],

                ['name' => 'rotation.policy', 'Value' => $backup->rotationPolicy],
                ['name' => 'rotation.cron', 'Value' => CronTranslator::translate($backup->rotationCron)],
                ['name' => 'rotation.count', 'Value' => $backup->rotationCount],

                ['name' => '<<readonly>>', 'Value' => $this->empty($backup->last)],
                ['name' => '<<readonly>>', 'Value' => $this->empty($backup->error)]
            ]
        );

        $this->warn("Name column indicate the key to use when change value with setup command,");
        $this->warn("Example: use backup:setup {$backup->name()} <<key>> <<value>>");

        return 0;
    }
}
