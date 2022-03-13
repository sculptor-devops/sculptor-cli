<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Tests\Dummy\RandomHome;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupsRepositoryTest extends TestCase
{
    use RandomHome;

    private Domains $repository;
    private Folders  $folders;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(Domains::class);

        $this->folders = $this->app->make(Folders::class);
    }

    /**
     * @throws Exception
     */
    public function test_backup_repository_create(): void
    {
        $this->repository->create('example_backup');

        $this->assertTrue(Filesystem::exists($this->folders->domains() . '/example_backup.yml'));
    }

    /**
     * @throws Exception
     */
    public function test_backup_repository_find_one(): void
    {
        $domain = $this->repository->find('example_backup');

        $this->assertEquals('example_backup', $domain->name);
    }

    /**
     * @throws Exception
     */
    public function test_backup_repository_find_all(): void
    {
        $domains = $this->repository->all();

        $this->assertCount(1, $domains);
    }

    /**
     * @throws Exception
     */
    public function test_backup_repository_delete(): void
    {
        $this->repository->delete('example_backup');

        $domains = $this->repository->all();

        $this->assertCount(0, $domains);
    }
}
