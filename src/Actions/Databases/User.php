<?php

namespace Sculptor\Agent\Actions\Databases;

use Exception;
use Sculptor\Agent\Actions\Databases\Support\Drivers;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Databases;
use Sculptor\Agent\Support\Password;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class User
{
    use Logging;

    public function __construct(private Databases $database, private Drivers $drivers, private Configuration $configuration)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name, string $user, ?string $password): string
    {
        $database = $this->database->find($name);

        $server = $this->drivers->make($database->driver);

        if (!$password) {
            $password = $this->configuration->password();
        }

        foreach ($this->database->all() as $item) {
            if ($item->has($user)) {
                throw new Exception("User $user already exists");
            }
        }

        if (!$server->user($user, $password, $database->name())) {
            throw new Exception("Error creating user: {$server->error()}");
        }

        $database->add($user, $password);

        $this->info($database, [], "database $name user $user added");

        $database->save();

        return $password;
    }
}
