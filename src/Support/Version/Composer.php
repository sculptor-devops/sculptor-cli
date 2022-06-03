<?php

namespace Sculptor\Agent\Support\Version;

use Exception;
use Sculptor\Foundation\Contracts\Runner;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Composer
{
    private array $available = [ '1.x', '2.x', '2.2.x' ];

    public function __construct(private Runner $runner)
    {
        //
    }

    public function url(string $version): string
    {
        return "https://getcomposer.org/download/latest-{$version}/composer.phar";
    }

    /**
     * @throws Exception
     */
    public function install(string $version, string $destination, string $name = 'composer'): void
    {

        $installer = '/tmp/composer-setup-' . time() . '.php';

        if (!copy($this->url($version), $installer)) {
            throw new Exception("Unable to download setup");
        }

        $result = $this->runner->run(['php', $installer, "--install-dir=$destination", "--filename=$name"]);

        if (!$result->success()) {
            throw new Exception("Composer installation error: {$result->error()}");
        }

        unlink($installer);
    }

    public function available(string $version): bool
    {
        return in_array($version, $this->available);
    }
}
