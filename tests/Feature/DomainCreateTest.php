<?php


namespace Feature;

use Exception;
use Sculptor\Agent\Configuration;
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
class DomainCreateTest extends TestCase
{
    use RandomHome;

    private Configuration $configuration;
    private RunnerDummy $runner;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->bind(Runner::class, RunnerDummy::class);

        $this->configuration = $this->app->make(Configuration::class);

        $this->runner = $this->app->make(Runner::class);
    }

    /**
     * @throws Exception
     */
    public function test_domain_creation(): void
    {
        $this->artisan('domain:create', [ 'name' => 'example1.org' ])->assertSuccessful();

        foreach ([ '', 'certs', 'configs', 'logs', 'shared', 'configs/metadata.yml' ] as $file) {

            $filename = "{$this->configuration->root()}/example1.org/$file";

            $this->assertTrue(file_exists($filename), "Directory $filename not found");
        }

        $this->assertTrue($this->runner->find('chmod -R 755 ' . $this->configuration->root()));

        $this->assertTrue($this->runner->find('chown -R www:www ' . $this->configuration->root()));
    }
}
