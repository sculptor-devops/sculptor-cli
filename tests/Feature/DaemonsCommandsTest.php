<?php

namespace Feature;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Enums\DaemonGroupType;
use Sculptor\Agent\Repositories\Databases;
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
class DaemonsCommandsTest extends TestCase
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

    public function test_daemons_operations(): void
    {
        foreach (['start', 'restart', 'stop', 'reload', 'enable', 'disable'] as $operation) {
            foreach (DaemonGroupType::toArray() as $group) {
                $this->artisan("daemon:$operation", [ 'group' => $group ])->assertSuccessful();
            }
        }
    }

    public function test_daemons_invalid_group(): void
    {
        $this->artisan("daemon:start", [ 'group' => 'non_existent' ])->assertFailed();
    }
}
