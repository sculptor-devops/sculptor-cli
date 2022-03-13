<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Domains;
use Enlightn\SecurityChecker\SecurityChecker;
use Sculptor\Agent\Support\Filesystem;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Security
{
    use Logging;

    public function __construct(private Domains $domains)
    {
        //s
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function run(string $name): array
    {
        $domain = $this->domains->find($name);

        $this->info($domain);

        $composerLock = $domain->current('composer.lock');

        $checker = new SecurityChecker();

        if (!Filesystem::exists($composerLock)) {
            throw new Exception("Cannot find composer.lock in $name");
        }

        return $checker->check($composerLock);
    }
}
