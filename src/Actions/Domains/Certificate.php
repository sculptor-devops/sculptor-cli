<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Stages\Services;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Actions\Domains\Support\Stage;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Certificate extends Stage
{
    use Logging;

    protected array $stages = [
        Certificate::class,
        Services::class
    ];

    /**
     * @throws Exception
     */
    public function run(string $name, string $hook): void
    {
        // register deploy pre
        $options = [ 'certbot.hook' => $hook ];

        $domain = $this->domains->find($name);

        $this->info($domain);

        foreach ($this->stages as $stage) {
            $step = $this->make($stage);

            $options = $step->prepare($domain, $options);
        }
    }
}
