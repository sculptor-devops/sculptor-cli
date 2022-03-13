<?php

namespace Sculptor\Agent\Repositories\Entities;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class User
{
    public function __construct(public string $name, public string $password)
    {
    }

    public function equal(string $name): bool
    {
        return $this->name == $name;
    }

    public function toArray(): array
    {
        return [ 'name' => $this->name, 'password' => $this->password ];
    }

    public static function make(array $parameters): User
    {
        return new User($parameters['name'], $parameters['password']);
    }
}
