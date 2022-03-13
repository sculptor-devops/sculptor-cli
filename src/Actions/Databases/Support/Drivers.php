<?php

namespace Sculptor\Agent\Actions\Databases\Support;

use Exception;
use Sculptor\Foundation\Contracts\Database;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Drivers
{
    public function __construct(private Database $mysql)
    {
    }

    /**
     * @throws Exception
     */
    public function make(string $driver): Database
    {
        switch ($driver) {
            case 'mysql':
                return $this->mysql;
        }

        throw new Exception("Database driver $driver not found");
    }
}
