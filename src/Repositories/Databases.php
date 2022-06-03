<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Repositories\Contracts\Repository;
use Sculptor\Agent\Repositories\Entities\Database;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Databases extends Repository
{
    /**
     * @throws Exception
     */
    public function path(): string
    {
        return $this->folders->databases();
    }

    public function name(): string
    {
        return 'database';
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function make(YmlFile $file): Database
    {
        return new Database($this->configuration, $file);
    }

    public function create(string $name, array $fields = null): Database
    {
        $database = parent::create($name, $fields);

        $database->save([
            'name' => $name
        ]);

        return $database;
    }

    public function find(string $name): Database
    {
        $item = parent::find($name);

        if (!$item->validate()) {
            throw new Exception("Invalid database data {$item->name()} missing " . join(', ', $item->missing()));
        }

        return $item;
    }
}
