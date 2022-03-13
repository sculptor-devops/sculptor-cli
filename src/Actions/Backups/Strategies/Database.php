<?php

namespace Sculptor\Agent\Actions\Backups\Strategies;

use Exception;
use Sculptor\Agent\Actions\Backups\Contracts\Strategy;
use Sculptor\Agent\Actions\Backups\Factories\Dumpers;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Databases;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Database implements Strategy
{
    public function __construct(private Configuration $configuration, private Databases $database, private Dumpers $dumpers)
    {
        //
    }

    public function name(): string
    {
        return 'database';
    }

    /**
     * @throws Exception
     */
    public function create(string $target): array
    {
        $database = $this->database->find($target);

        $dumper = $this->dumpers->make($database->driver);

        $filename = $this->configuration->get('temp');

        $dumper->dump($database->name(), $filename);

        return [
            $filename => "$target.sql"
        ];
    }

    /**
     * @throws Exception
     */
    public function meta(string $target): array
    {
        $database = $this->database->find($target);

        return $database->all();
    }
}
