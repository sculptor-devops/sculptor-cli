<?php

namespace Sculptor\Agent\Actions\Backups;

use Exception;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\BackupStatusType;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Validation\Validator;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Create
{
    use Logging;

    public function __construct(private Backups $backups, private Configuration $configuration)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name, string $resource, string $target): void
    {
        Validator::make('Backup')->validateKeysValues([
            'name' => $name,
            'resource' => $resource,
            'target' => $target
        ]);

        $backup = $this->backups->create($name);

        $this->info($backup, [], 'created');

        $backup->save([
            'status' => BackupStatusType::NONE,
            'resource' => $resource,
            'target' => $target,
            'cron' => $this->configuration->get('backup.cron'),
            'compression' => $this->configuration->get('backup.compression'),
            'temp' => $this->configuration->get('backup.temp'),

            'archive.driver' => $this->configuration->get('backup.archive.driver'),
            'archive.path' => $this->configuration->get('backup.archive.path'),

            'rotation.cron' => $this->configuration->get('backup.rotation.cron'),
            'rotation.policy' => $this->configuration->get('backup.rotation.policy'),
            'rotation.count' => $this->configuration->get('backup.rotation.count'),
        ]);
    }
}
