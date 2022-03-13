<?php

namespace Tests\Dummy;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Support\Filesystem;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
trait RandomHome
{
    /**
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        $prefix = testPrefix();

        $class = Str::replace('\\', '', __CLASS__);

        $_ENV['SCULPTOR_HOME'] = "/tmp/sculptor_test/{$prefix}/{$class}/home";
        $_ENV['SCULPTOR_SITES'] = "/tmp/sculptor_test/{$prefix}/{$class}/sites";
        $_ENV['SCULPTOR_ETC'] = "/tmp/sculptor_test/{$prefix}/{$class}/etc";
    }

    /**
     * @throws Exception
     */
    public function etc(string $name): void
    {
        foreach ([
                     env('SCULPTOR_ETC') . "/letsencrypt/live/{$name}",
                     env('SCULPTOR_ETC') . "/nginx/sites-enabled",
                     env('SCULPTOR_ETC') . "/supervisor/conf.d",
                     env('SCULPTOR_ETC') . "/logrotate.d",
                 ] as $path) {
            Filesystem::makeDirectoryRecursive($path);

            foreach (['cert.pem', 'chain.pem', 'fullchain.pem', 'privkey.pem'] as $filename) {
                Filesystem::touch("{$path}/{$filename}");
            }
        }
    }
}
