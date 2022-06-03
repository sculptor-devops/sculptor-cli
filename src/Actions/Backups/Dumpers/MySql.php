<?php

namespace Sculptor\Agent\Actions\Backups\Dumpers;

use Sculptor\Agent\Actions\Backups\Contracts\Dumper;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\BackupDatabaseType;
use Spatie\DbDumper\Databases\MySql as Driver;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class MySql implements Dumper
{
    public function __construct(private Configuration $configuration)
    {
        //
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return BackupDatabaseType::MYSQL;
    }

    public function dump(string $name, string $filename): bool
    {
        Driver::create()
            ->setHost($this->configuration->get('database.host'))
            ->setPort($this->configuration->get('database.port'))
            ->setDbName($name)
            ->setUserName('root')
            ->setPassword($this->configuration->databasePassword())
            ->dumpToFile($filename);

        return true;
    }
}
