<?php

namespace Sculptor\Agent\Actions\Backups\Strategies;

use Exception;
use Sculptor\Agent\Actions\Backups\Contracts\Strategy;
use Sculptor\Agent\Support\Folders;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Blueprint implements Strategy
{
    public function __construct(private Folders $folders)
    {
        //
    }

    public function name(): string
    {
        return 'blueprint';
    }

    /**
     * @throws Exception
     */
    public function create(string $target): array
    {
        return [
            $this->folders->configuration() => "configurations"
        ];
    }

    /**
     * @throws Exception
     */
    public function meta(string $target): array
    {
        return [];
    }
}
