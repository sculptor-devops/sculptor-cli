<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Repositories\Contracts\Repository;
use Sculptor\Agent\Repositories\Entities\Alarm;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Repositories\Entities\Database;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Alarms extends Repository
{
    /**
     * @throws Exception
     */
    public function path(): string
    {
        return $this->folders->alarms();
    }

    public function name(): string
    {
        return 'alarm';
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function make(YmlFile $file): Alarm
    {
        return new Alarm($this->configuration, $file);
    }

    public function create(string $name, array $fields = null): Alarm
    {
        $alarm = parent::create($name, $fields);

        $alarm->save([
            'name' => $name
        ]);

        return $alarm;
    }

    /**
     * @throws Exception
     */
    public function runnable(): array
    {
        $result = [];

        foreach ($this->all() as $alarm) {
            if ($alarm->runnable()) {
                $result[] = $alarm;
            }
        }

        return $result;
    }

    public function find(string $name): Alarm
    {
        $item = parent::find($name);

        if (!$item->validate()) {
            throw new Exception("Invalid alarm data {$item->name()} missing " . join(', ', $item->missing()));
        }

        return $item;
    }
}
