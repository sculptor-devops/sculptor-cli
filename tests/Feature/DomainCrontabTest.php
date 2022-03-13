<?php


namespace Feature;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Support\Folders;
use Sculptor\Foundation\Contracts\Runner;
use Tests\Dummy\RandomHome;
use Tests\TestCase;
use Tests\Dummy\Runner as RunnerDummy;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainCrontabTest extends TestCase
{
    use RandomHome;

    private Configuration $configuration;
    private RunnerDummy $runner;
    private Domains $domains;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->bind(Runner::class, RunnerDummy::class);

        $this->configuration = $this->app->make(Configuration::class);

        $this->runner = $this->app->make(Runner::class);

        $this->domains = $this->app->make(Domains::class);

        $this->etc('example-crontab.org');
    }

    /**
     * @throws Exception
     */
    public function test_domain_crontab_add(): void
    {
        $this->artisan('domain:create', [ 'name' => 'example-crontab.org' ])->assertSuccessful();

        $this->artisan('domain:crontab', [ 'name' => 'example-crontab.org', 'operation' => 'add', 'schedule' => '* * * * *', 'shell' => 'ls -l' ])->assertSuccessful();

        $domain = $this->domains->find('example-crontab.org');

        $crontab = $domain->crontabArray;

        $this->assertCount(1, $crontab);

        $this->assertEquals(["schedule" => "* * * * *", "command" => "ls -l"], $crontab[0]);
    }

    /**
     * @throws Exception
     */
    public function test_domain_crontab_delete(): void
    {
        $this->artisan('domain:crontab', [ 'name' => 'example-crontab.org', 'operation' => 'add', 'schedule' => '0 0 * * *', 'shell' => 'ls -ltr' ])->assertSuccessful();

        $domain = $this->domains->find('example-crontab.org');

        $crontab = $domain->crontabArray;

        $this->assertCount(2, $crontab);

        $this->assertEquals([
                                ["schedule" => "* * * * *", "command" => "ls -l"],
                                ["schedule" => "0 0 * * *", "command" => "ls -ltr"],
                            ], $crontab);

        $this->artisan('domain:crontab', [ 'name' => 'example-crontab.org', 'operation' => 'del', 'schedule' => 0 ])->assertSuccessful();

        $domain = $this->domains->find('example-crontab.org');

        $crontab = $domain->crontabArray;

        $this->assertCount(1, $crontab);
    }
}
