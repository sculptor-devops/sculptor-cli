<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Repositories\Databases;
use Sculptor\Agent\Repositories\Entities\User;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Tests\Dummy\RandomHome;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DatabasesRepositoryTest extends TestCase
{
    use RandomHome;

    private Databases $repository;
    private Folders  $folders;
    private string $name = 'database_name';

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(Databases::class);

        $this->folders = $this->app->make(Folders::class);
    }

    /**
     * @throws Exception
     */
    public function test_database_repository_create(): void
    {
        $this->repository->create('database_name');

        $this->assertTrue(Filesystem::exists($this->folders->databases() . "/{$this->name}.yml"));
    }

    /**
     * @throws Exception
     */
    public function test_database_repository_find_one(): void
    {
        $database = $this->repository->find($this->name);

        $this->assertEquals('database_name', $database->name);
    }

    /**
     * @throws Exception
     */
    public function test_database_repository_record(): void
    {
        $database = $this->repository->find($this->name);

        $this->assertEquals('database_name', $database->name);

        $this->assertCount(0, $database->users());

        $database->add('user1', 'password1');

        $this->assertCount(1, $database->users());

        $database->add('user2', 'password2');

        $this->assertCount(2, $database->users());

        $database->delete('user1');

        $this->assertCount(1, $database->users());

        $this->assertEquals('password2', $database->user('user2')->password);

        $this->assertInstanceOf(User::class, $database->user('user2'));

        $database->save();

        $database = $this->repository->find($this->name);

        $this->assertCount(1, $database->users());

        $this->expectException(Exception::class);

        $database->user('user3');
    }

    /**
     * @throws Exception
     */
    public function test_database_repository_find_all(): void
    {
        $databases = $this->repository->all();

        $this->assertCount(1, $databases);
    }

    /**
     * @throws Exception
     */
    public function test_database_repository_delete(): void
    {
        $this->repository->delete($this->name);

        $databases = $this->repository->all();

        $this->assertCount(0, $databases);
    }
}
