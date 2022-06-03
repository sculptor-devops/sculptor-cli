<?php

namespace Sculptor\Agent\Actions\Backups\Support;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Foundation\Support\Version;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Metadata
{
    public function __construct(private Configuration $configuration, private Version $version)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function make(string $filename, array $data): void
    {
        $header = [
            'version' => 1,
            'sculptor' => composerVersion(),
            'root' => $this->configuration->root(),
            'db' => $this->configuration->get('database.default'),
            'os' => $this->version->name(),
            'arch' => $this->version->arch(),
            'bits' => $this->version->bits()
        ];

        Filesystem::putYml($filename, $header + $data);
    }
}
