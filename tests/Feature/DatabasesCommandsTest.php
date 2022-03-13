<?php


namespace Feature;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Databases;
use Sculptor\Agent\Repositories\Entities\Database;
use Sculptor\Agent\Repositories\Entities\User;
use Sculptor\Foundation\Contracts\Runner;
use Tests\Dummy\RandomHome;
use Tests\Dummy\MySql;
use Sculptor\Foundation\Database\MySql as MySqlInterface;
use Tests\TestCase;
use Tests\Dummy\Runner as RunnerDummy;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DatabasesCommandsTest extends TestCase
{
    use RandomHome;

    private Configuration $configuration;
    private RunnerDummy $runner;
    private Databases $databases;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->bind(Runner::class, RunnerDummy::class);

        $this->app->bind(MySqlInterface::class, MySql::class);

        $this->configuration = $this->app->make(Configuration::class);

        $this->runner = $this->app->make(Runner::class);

        $this->databases = $this->app->make(Databases::class);
    }

    /**
     * @throws Exception
     */
    public function test_database_create(): void
    {
        $this->artisan('database:create', [ 'name' => 'example_db' ])->assertSuccessful();

        $this->assertTrue($this->databases->exists('example_db'));

        $database = $this->databases->find('example_db');

        $this->assertInstanceOf(Database::class, $database);

        $this->assertEquals('example_db', $database->name);

        $this->assertEquals('mysql', $database->driver);
    }

    /**
     * @throws Exception
     */
    public function test_database_user_create(): void
    {
        $this->artisan('database:user', [ 'database' => 'example_db', 'name' => 'user1', 'password' => '123456' ])->assertSuccessful();

        $database = $this->databases->find('example_db');

        $this->assertCount(1, $database->users());

        $this->artisan('database:user', [ 'database' => 'example_db', 'name' => 'user2' ])->assertSuccessful();

        $database = $this->databases->find('example_db');

        $this->assertCount(2, $database->users());

        $users = $database->users();

        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);

            $this->assertTrue(in_array($user->name, ['user1', 'user2']));

            $this->assertNotNull($user->password);
        }
    }

    /**
     * @throws Exception
     */
    public function test_database_user_delete(): void
    {
        $this->artisan('database:user_delete', [ 'database' => 'example_db', 'name' => 'user1' ])->assertSuccessful();

        $database = $this->databases->find('example_db');

        $users = $database->users();

        $this->assertCount(1, $users);

        $this->assertEquals('user2', collect($users)->first()->name);
    }

    /**
     * @throws Exception
     */
    public function test_database_delete(): void
    {
        $this->expectException(Exception::class);

        $this->artisan('database:delete', [ 'name' => 'example_db' ])->assertSuccessful();

        $this->assertTrue($this->databases->exists('example_db'));

        $this->artisan('database:user_delete', [ 'database' => 'example_db', 'name' => 'user2' ])->assertSuccessful();

        $this->artisan('database:delete', [ 'name' => 'example_db' ])->assertSuccessful();

        $this->assertFalse($this->databases->exists('example_db'));

        $this->expectException(Exception::class);

        $this->databases->find('example_db');
    }
}
