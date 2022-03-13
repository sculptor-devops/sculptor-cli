<?php

namespace Tests\Unit;

use Exception;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Sculptor\Agent\Actions\Alarms\Factories\Subjects;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Actions\Backups\Archive;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Support\Chronometer;
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
class SubjectBackupTest extends TestCase
{
    use RandomHome;

    private Subjects $factory;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->app->make(Subjects::class);

        $this->backups = $this->app->make(Backups::class);

        $this->archive = $this->app->make(Archive::class);

        $this->app->bind(Runner::class, RunnerDummy::class);

        $this->app->bind(MySqlInterface::class, MySql::class);

        $this->etc('example-backup.org');
    }

    /**
     * @throws Exception
     */
    public function test_backup(): void
    {
        $this->artisan('domain:create', [ 'name' => 'example-backup.org' ])->assertSuccessful();

        $this->artisan('domain:prepare', [ 'name' => 'example-backup.org' ])->assertSuccessful();

        $this->artisan("backup:create", [ 'name' => 'example_backup', 'resource' => 'domain', 'target' => 'example-backup.org' ])->assertSuccessful();

        $this->artisan('backup:make', [ 'name' => 'example_backup' ])->assertSuccessful();

        $monitor = $this->factory->make('backup');

        $this->assertTrue($monitor->parameters(Parameters::parse("name:=example_backup"))->value() == 1);
    }
}
