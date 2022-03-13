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
class DomainWorkerTest extends TestCase
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

        $this->etc('example-workers.org');
    }

    /**
     * @throws Exception
     * {PHP} {CURRENT}/artisan queue:work --daemon
     */
    public function test_domain_worker_add(): void
    {
        $this->artisan('domain:create', [ 'name' => 'example-workers.org' ])->assertSuccessful();

        $this->artisan('domain:workers', [ 'name' => 'example-workers.org', 'operation' => 'add', 'prefix' => 'queue', 'shell' => '{PHP} {CURRENT}/ls -l', 'count' => 2 ])->assertSuccessful();

        $domain = $this->domains->find('example-workers.org');

        $workers = $domain->workersArray;

        $this->assertCount(1, $workers);

        $this->assertEquals([ "prefix" => "queue", "command" => "{PHP} {CURRENT}/ls -l", "count" => 2], $workers[0]);
    }

    /**
     * @throws Exception
     */
    public function test_domain_worker_delete(): void
    {
        $this->artisan('domain:workers', [ 'name' => 'example-workers.org', 'operation' => 'add', 'prefix' => 'emails', 'shell' => '{PHP} {CURRENT}/ls -ltr' ])->assertSuccessful();

        $domain = $this->domains->find('example-workers.org');

        $workers = $domain->workersArray;

        $this->assertCount(2, $workers);

        $this->assertEquals([
                                [ 'command' => '{PHP} {CURRENT}/ls -l', 'prefix' => 'queue', 'count' => 2 ],
                                [ 'command' => '{PHP} {CURRENT}/ls -ltr', 'prefix' => 'emails', 'count' => 1 ]
                            ], $workers);

        $this->artisan('domain:workers', [ 'name' => 'example-workers.org', 'operation' => 'delete', 'prefix' => 0 ])->assertSuccessful();

        $domain = $this->domains->find('example-workers.org');

        $workers = $domain->workersArray;

        $this->assertCount(1, $workers);
    }
}
