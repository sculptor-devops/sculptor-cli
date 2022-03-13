<?php


namespace Sculptor\Agent\Repositories;


use Exception;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Repositories\Contracts\Repository;
use Sculptor\Agent\Repositories\Entities\Backup;
use Sculptor\Agent\Repositories\Entities\Database;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Backups extends Repository
{
    /**
     * @throws Exception
     */
    function path(): string
    {
        return $this->folders->backups();
    }

    function name(): string
    {
        return 'backup';
    }

    /**
     * @throws InvalidConfigurationException
     */
    function make(YmlFile $file): Backup
    {
        return new Backup($this->configuration, $file);
    }

    function create(string $name, array $fields = null): Backup
    {
        $backup = parent::create($name, $fields);

        $backup->save([
            'name' => $name
        ]);

        return $backup;
    }

    /**
     * @throws Exception
     */
    public function runnable(): array
    {
        $result = [];

        foreach ($this->all() as $backup) {
            if ($backup->runnable()) {
                $result[] = $backup;
            }
        }

        return $result;
    }

    public function find(string $name): Backup
    {
        $item = parent::find($name);

        if (!$item->validate()) {
            throw new Exception("Invalid backup data {$item->name()} missing " . join(', ', $item->missing()) );
        }

        return $item;
    }
}
