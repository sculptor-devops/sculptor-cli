<?php

namespace Sculptor\Agent\Repositories;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Repositories\Contracts\Repository;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Domains extends Repository
{
    /**
     * @throws Exception
     */
    public function path(): string
    {
        return $this->folders->domains();
    }

    public function name(): string
    {
        return 'domain';
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function make(YmlFile $file): Domain
    {
        return new Domain($this->configuration, $file);
    }

    public function create(string $name, array $fields = null): Domain
    {
        foreach ($this->all() as $domain) {
            if (Str::contains($domain->aliases, $name)) {
                throw new Exception("Domain $name already exists in {$domain->name()} aliases");
            }
        }

        $domain = parent::create($name, $fields);

        $domain->save([
            'name' => $name,
            'token' => $this->password->token(),
            'created' => now()
        ]);

        return $domain;
    }

    public function find(string $name): Domain
    {
        $item = parent::find($name);

        if (!$item->validate()) {
            throw new Exception("Invalid domain data {$item->name()} missing " . join(', ', $item->missing()));
        }

        return $item;
    }

    /**
     * @throws Exception
     */
    public function findByToken(string $token): Domain
    {
        foreach ($this->all() as $item) {
            if ($item->token == $token) {
                return $item;
            }
        }

        throw new Exception("Cannot find domain token $token");
    }
}
