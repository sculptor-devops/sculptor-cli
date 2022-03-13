<?php

namespace Sculptor\Agent\Actions\Backups\Strategies;

use Exception;
use Sculptor\Agent\Actions\Backups\Contracts\Strategy;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Domains;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Domain implements Strategy
{
    public function __construct(private Domains $domains, private Configuration $configuration)
    {
        //
    }

    public function name(): string
    {
        return 'domain';
    }

    /**
     * @throws Exception
     */
    public function create(string $target): array
    {
        $domain = $this->domains->find($target);

        $path = $domain->root();

        return [
            "$path/shared" => "shared",
            "$path/configs" => "configs",
            "$path/certs" => "certs"
        ];
    }

    /**
     * @throws Exception
     */
    public function meta(string $target): array
    {
        $domain = $this->domains->find($target);

        return $domain->all();
    }
}
