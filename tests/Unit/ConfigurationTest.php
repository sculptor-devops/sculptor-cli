<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Configuration;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class ConfigurationTest extends TestCase
{
    private Configuration $configuration;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->configuration = $this->app->make(Configuration::class);
    }

    /**
     * @throws Exception
     */
    public function test_configuration_load(): void
    {
         $this->assertEquals([
            "log_level",
            "root",
            "webhook",
            "php.version",
            "php.user",
            "database.default",
            "database.host",
            "database.port",
            "security.password.min",
            "security.password.max",
            "monitors.samples",
            "monitors.format",
            "monitors.cron",
            "monitors.disks.0.name",
            "monitors.disks.0.root",
            "backup.cron",
            "backup.compression",
            "backup.temp",
            "backup.archive.driver",
            "backup.archive.path",
            "backup.rotation.cron",
            "backup.rotation.policy",
            "backup.rotation.count",
            "backup.drivers.dropbox.case_sensitive",
            "backup.drivers.dropbox.key",
            "backup.drivers.s3.key",
            "backup.drivers.s3.secret",
            "backup.drivers.s3.bucket",
            "backup.drivers.s3.region",
            "backup.drivers.s3.endpoint",
         ], $this->configuration->keys());

         $this->assertNotNull($this->configuration->securityKey());
    }

    public function test_configuration_version(): void
    {
        $this->assertEquals( 1, $this->configuration->version());
    }
}
