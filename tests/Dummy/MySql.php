<?php

namespace Tests\Dummy;

use Sculptor\Foundation\Contracts\Database;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class MySql implements Database
{
    public function db(string $name): bool
    {
        return true;
    }

    public function drop(string $name): bool
    {
        return true;
    }

    public function user(string $user, string $password, string $db, string $host = 'localhost'): bool
    {
        return true;
    }

    public function dropUser(string $user, string $host = 'localhost'): bool
    {
        return true;
    }

    public function password(string $user, string $password, string $db, string $host = 'localhost'): bool
    {
        return true;
    }

    public function error(): string
    {
        return 'Dummy mysql driver';
    }
}
