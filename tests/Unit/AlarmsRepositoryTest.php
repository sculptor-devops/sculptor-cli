<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Repositories\Alarms;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Tests\Dummy\RandomHome;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class AlarmsRepositoryTest extends TestCase
{
    use RandomHome;

    private Alarms $repository;
    private Folders  $folders;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(Alarms::class);

        $this->folders = $this->app->make(Folders::class);
    }

    /**
     * @throws Exception
     */
    public function test_alarms_repository_create(): void
    {
        $this->repository->create('example_alarm');

        $this->assertTrue(Filesystem::exists($this->folders->alarms() . '/example_alarm.yml'));
    }

    /**
     * @throws Exception
     */
    public function test_alarms_repository_find_one(): void
    {
        $alarm = $this->repository->find('example_alarm');

        $this->assertEquals('example_alarm', $alarm->name);
    }

    /**
     * @throws Exception
     */
    public function test_alarms_repository_find_all(): void
    {
        $alarms = $this->repository->all();

        $this->assertCount(1, $alarms);
    }

    /**
     * @throws Exception
     */
    public function test_backup_repository_delete(): void
    {
        $this->repository->delete('example_alarm');

        $alarms = $this->repository->all();

        $this->assertCount(0, $alarms);
    }
}
