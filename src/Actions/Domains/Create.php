<?php

namespace Sculptor\Agent\Actions\Domains;

use Exception;
use Sculptor\Agent\Actions\Domains\Stages\Certificates;
use Sculptor\Agent\Actions\Domains\Stages\Permissions;
use Sculptor\Agent\Actions\Domains\Stages\Structure;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Actions\Domains\Support\Stage;
use Sculptor\Agent\Enums\DomainStatusType;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Create extends Stage
{
    use Logging;

    protected array $stages = [
        Structure::class,
        Certificates::class,
        Permissions::class
    ];

    /**
     * @throws Exception
     */
    public function run(string $name, string $template, bool $force): void
    {
        $options = [];

        $domain = null;

        if ($force && $this->domains->exists($name)) {
            $domain = $this->domains->find($name);
        }

        if (!$domain) {
            $domain = $this->domains->create($name);
        }

        $this->info($domain);

        foreach ($this->stages as $stage) {
            $step = $this->make($stage);

            $options = $step->create($domain, $options);
        }

        $domain->save([
            'name' => $name,
            'status' => DomainStatusType::NEW,
            'user' => $this->configuration->get('php.user'),
            'engine' => $this->configuration->get('php.version'),
            'template' => $template,
            'created' => now()
        ]);
    }
}
