<?php

namespace Sculptor\Agent\Repositories\Entities;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\BackupStatusType;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Repositories\Contracts\Entity as EntityInterface;
use Sculptor\Agent\Repositories\Support\Entity;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 * @property bool $enabled
 * @property string $status
 * @property string $resource
 * @property string $target
 * @property string $archiveDriver
 * @property string $rotationPolicy
 * @property string $cron
 * @property string $last
 * @property string $archivePath
 * @property int $rotationCount
 * @property string $compression
 * @property string $error
 * @property string $temp
 * @property string $rotationCron
 * @property string $checksum
 */
class Backup extends Entity implements EntityInterface
{
    protected array $fields = [ ];

    /**
     * @throws InvalidConfigurationException
     */
    public function __construct(Configuration $configuration, YmlFile $yml)
    {
        parent::__construct($configuration, $yml);

        $yml->verify(1);
    }

    public function runnable(): bool
    {
        return $this->enabled && $this->status != BackupStatusType::RUNNING;
    }

    /**
     * @throws Exception
     */
    public function success(): void
    {
        $this->save([ 'status' => BackupStatusType::OK, 'last' => now(), 'error' => '' ]);
    }

    /**
     * @throws Exception
     */
    public function error(string $error): void
    {
        $this->save(['status' => BackupStatusType::ERROR, 'last' => now(), 'error' => $error]);
    }

    /**
     * @throws Exception
     */
    public function running(): void
    {
        $this->save(['status' => BackupStatusType::RUNNING, 'last' => now()]);
    }
}
