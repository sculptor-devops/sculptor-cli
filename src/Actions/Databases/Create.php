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

class Create
{
    use Logging;

    public function __construct(private Databases $database, private Drivers $drivers)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name, string $driver): void
    {
        $server = $this->drivers->make($driver);

        if (!$server->db($name)) {
            throw new Exception("Database driver error: {$server->error()}");
        }

        $database = $this->database->create($name, [
            'driver' => $driver
        ]);

        $this->info($database, [], "$driver database created");
    }
}
