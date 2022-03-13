<?php

namespace Feature;

use Exception;
use Sculptor\Agent\Actions\Backups\Archive;
use Sculptor\Agent\Actions\Backups\Support\Tag;
use Sculptor\Agent\Exceptions\ValidationException;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Support\Filesystem;
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
class BackupsRotateTest extends TestCase
{
    use RandomHome;

    private Backups $backups;

    private Archive $archive;

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

        $this->archive = $this->app->make(Archive::class);

        $this->etc('example-backup.org');
    }

    /**
     * @throws Exception
     */
    public function test_backup_rotate(): void
    {
        $this->artisan('domain:create', [ 'name' => 'example-rotation.org' ])->assertSuccessful();

        $this->artisan('domain:prepare', [ 'name' => 'example-rotation.org' ])->assertSuccessful();

        $this->artisan("backup:create", [ 'name' => 'example_rotation', 'resource' => 'domain', 'target' => 'example-rotation.org' ])->assertSuccessful();

        $this->artisan("backup:setup", [ 'name' => 'example_rotation', 'key' => 'rotation.policy', 'value' => 'count' ])->assertSuccessful();

        $this->artisan("backup:setup", [ 'name' => 'example_rotation', 'key' => 'rotation.count', 'value' => '1' ])->assertSuccessful();

        for($i = 0; $i < 2; $i++) {
            sleep(1);

            $this->artisan('backup:make', [ 'name' => 'example_rotation' ])->assertSuccessful();
        }

        $this->artisan('backup:rotate', [ 'name' => 'example_rotation' ])->assertSuccessful();

        $this->assertGreaterThan(0, $this->archive->all('example_rotation'));
    }
}
