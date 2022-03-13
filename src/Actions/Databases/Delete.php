<?php

namespace Sculptor\Agent\Actions\Databases;

use Exception;
use Sculptor\Agent\Actions\Databases\Support\Drivers;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Databases;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Delete
{
    use Logging;

    public function __construct(private Databases $database, private Drivers $drivers)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name): void
    {
        $database = $this->database->find($name);

        $server = $this->drivers->make($database->driver);

        if (count($database->users()) > 0) {
            throw new Exception("Cannot delete a database $name with users");
        }

        if (!$server->drop($name)) {
            throw new Exception("Database driver error: {$server->error()}");
        }

        $this->database->delete($name);

        $this->info($database, [], "$name database deleted");
    }
}
