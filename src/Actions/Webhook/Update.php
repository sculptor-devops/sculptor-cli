<?php

namespace Sculptor\Agent\Actions\Webhook;

use Exception;
use Sculptor\Agent\Actions\Webhook\Support\Repository;
use Sculptor\Agent\Repositories\Domains;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Update
{
    public function __construct(private Repository $webhook, private Domains $domains)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function __invoke(): array
    {
        $result = [];

        foreach ($this->domains->all() as $domain) {
            if ($this->webhook->exists($domain->token)) {
                continue;
            }

            $this->webhook->insert($domain);

            $result[] = $domain;
        }

        return $result;
    }
}
