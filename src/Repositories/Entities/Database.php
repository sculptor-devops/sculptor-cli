<?php

namespace Sculptor\Agent\Repositories\Entities;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Repositories\Contracts\Entity as EntityInterface;
use Sculptor\Agent\Repositories\Support\Entity;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 * @property string $driver
 */
class Database extends Entity implements EntityInterface
{
    protected array $fields = [ ];

    /**
     * @throws InvalidConfigurationException
     */
    public function __construct(Configuration $configuration, YmlFile $yml)
    {
        parent::__construct($configuration, $yml);

        $yml->verify(1);
    }

    public function users(): array
    {
        return collect($this->content->getArray('users'))
                ->map(fn($item) => User::make($item))
                ->toArray();
    }

    public function add(string $user, string $password): void
    {
        $users = $this->users();

        $users[] = new User($user, $password);

        $users = collect($users)->map(fn(User $user) => $user->toArray())->toArray();

        $this->content->setArray('users', $users);
    }

    public function delete(string $user): void
    {
        $users = collect($this->users())
            ->filter(fn(User $item) => !$item->equal($user))
            ->map(fn(User $user) => $user->toArray())
            ->toArray();

        $this->content->setArray('users', $users);
    }

    /**
     * @throws Exception
     */
    public function user(string $user): User
    {
        $users = collect($this->users())
                    ->filter(fn(user $item) => $item->equal($user));

        if ($users->count() == 1) {
            return $users->first();
        }

        throw new Exception("Database user $user not found");
    }

    public function has(string $user): bool
    {
        $users = collect($this->users())
            ->filter(fn(user $item) => $item->equal($user));

        if ($users->count() == 1) {
            return true;
        }

        return false;
    }
}
