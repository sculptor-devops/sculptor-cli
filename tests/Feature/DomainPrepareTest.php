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
class DomainPrepareTest extends TestCase
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

        $this->etc('example1.org');
    }

    /**
     * @throws Exception
     */
    public function test_domain_prepare(): void
    {
        $this->artisan('domain:create', [ 'name' => 'example1.org' ])->assertSuccessful();

        $domain = $this->domains->find('example1.org');

        $domain->crontabArray = [ [ 'schedule' => '* * * * *', 'command' => '{PHP} {CURRENT}/artisan schedule:run >> /dev/null 2>&1']];

        $domain->workersArray = [ [ 'prefix' => 'queue', 'command' => '{PHP} {CURRENT}/artisan queue:work --daemon', 'count' => 2 ] ];

        $domain->gitUrl = 'https://github.com/laravel/laravel';

        $domain->gitBranch = 'master';

        $domain->save();

        $this->artisan('domain:prepare', [ 'name' => 'example1.org' ])->assertSuccessful();

        $this->assertTrue(file_exists( $domain->public('index.html')));
    }
}
