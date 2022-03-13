<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Version\Php;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class PhpVersionsTest extends TestCase
{
    public function test_version_exists(): void
    {
        $version = new Php();

        $this->assertGreaterThan(0, count($version->available()));

        $this->assertTrue($version->installed(phpversion('tidy')));
    }
}
