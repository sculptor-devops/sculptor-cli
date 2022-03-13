<?php

namespace Feature;

use Exception;
use Sculptor\Agent\Exceptions\ValidationException;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Support\Folders;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Database\MySql as MySqlInterface;
use Tests\Dummy\MySql;
use Tests\Dummy\RandomHome;
use Tests\Dummy\Runner as RunnerDummy;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupsManageTest extends TestCase
{
    use RandomHome;

    private Backups $backups;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->folders = $this->app->make(Folders::class);

        $this->app->bind(Runner::class, RunnerDummy::class);

        $this->app->bind(MySqlInterface::class, MySql::class);

        $this->backups = $this->app->make(Backups::class);
    }

    public function test_backup_create(): void
    {
        $this->artisan('domain:create', [ 'name' => 'example-backup.org' ])->assertSuccessful();

        $this->artisan("backup:create", [ 'name' => 'example_backup', 'resource' => 'domain', 'target' => 'example-backup.org' ])->assertSuccessful();
    }

    public function test_backup_create_wrong_resource(): void
    {
        $this->expectException(ValidationException::class);

        $this->artisan("backup:create", [ 'name' => 'example_backup', 'resource' => 'wrong', 'target' => 'example-backup1.org' ]);
    }

    public function test_backup_create_wrong_target(): void
    {
        $this->expectException(Exception::class);

        $this->artisan("backup:create", [ 'name' => 'example_backup', 'resource' => 'wrong', 'target' => 'example-backup2.org' ]);
    }

    public function parameters(): array
    {
        return [
            ['resource', 'blueprint'],
            ['resource', 'database'],
            ['resource', 'domain'],
            ['target', 'example-backup.org'],
            ['status', 'ok'],
            ['cron', '* * * * *'],
            ['compression', 'zip'],

            ['archive.driver', 'local'],
            ['archive.path', '/tmp'],

            ['rotation.cron', '* * * * *'],
            ['rotation.policy', 'days'],
            ['rotation.count', '10'],
        ];
    }

    /**
     * @dataProvider parameters
     * @throws Exception
     */
    public function test_backup_setup(string $key, string $value): void
    {
        $this->artisan("backup:setup", [ 'name' => 'example_backup', 'key' => $key, 'value' => $value ])->assertSuccessful();

        $backup = $this->backups->find('example_backup');

        $this->assertEquals($value, $backup->get($key));
    }

    public function wrongParameters(): array
    {
        return [
            ['resource', 'blueprint_wrong'],
            ['resource', 'database_wrong'],
            ['resource', 'domain_wrong'],
            ['target', 'example-backup.org_wrong'],
            ['status', 'ok_wrong'],
            ['cron', '* * * * *_wrong'],
            ['compression', 'zip_wrong'],

            ['archive.driver', 'local_wrong'],
            // ['archive.path', '/tmp_wrong'],

            ['rotation.cron', '* * * * *_wrong'],
            ['rotation.policy', 'days_wrong'],
            ['rotation.count', '10_wrong'],
        ];
    }

    /**
     * @dataProvider wrongParameters
     * @throws Exception
     */
    public function test_backup_wrong_setup(string $key, string $value): void
    {
        $this->expectException(ValidationException::class);

        $this->artisan("backup:setup", [ 'name' => 'example_backup', 'key' => $key, 'value' => $value ]);

        $backup = $this->backups->find('example_backup');

        $this->assertNotEquals($value, $backup->get($key));
    }

    /**
     * @throws Exception
     */
    public function test_backup_delete(): void
    {
        $this->artisan("backup:delete", [ 'name' => 'example_backup' ]);

        $this->assertFalse($this->backups->exists('example_backup'));
    }
}
