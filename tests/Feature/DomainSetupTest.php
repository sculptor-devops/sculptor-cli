<?php


namespace Feature;


use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Exceptions\ValidationException;
use Sculptor\Agent\Repositories\Domains;
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
class DomainSetupTest extends TestCase
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

        $this->app->bind(MySqlInterface::class, MySql::class);

        $this->configuration = $this->app->make(Configuration::class);

        $this->runner = $this->app->make(Runner::class);

        $this->domains = $this->app->make(Domains::class);

        $this->etc('example-setup.org');
    }

    /**
     * @throws Exception
     */
    public function test_domain_setup_parameters(): void
    {
        $this->artisan('database:create', [ 'name' => 'example_db' ])->assertSuccessful();

        $this->artisan('database:user', [ 'database' => 'example_db', 'name' => 'user1', 'password' => '123456' ])->assertSuccessful();

        $this->artisan('domain:create', [ 'name' => 'example-setup.org' ])->assertSuccessful();

        foreach ([
                    'www' => 'false',
                    'aliases' => 'example.org example.com',
                    'template' => 'generic',
                    'certificate.type' => 'custom',
                    'certificate.email' => 'name@example-setup.org',
                    'engine' => '8.0',
                    'database.name' => 'example_db',
                    'database.user' => 'user1',
                    'deploy.command' => 'deploy:all',
                    'deploy.install' => 'deploy:setup',
                    'git.url' => 'https://github.com/some/repository',
                    'git.branch' => 'main',
                 ] as $key => $value) {
            $this->artisan('domain:setup', [ 'name' => 'example-setup.org', 'key' => $key, 'value' => $value ])->assertSuccessful();
        }

        $domain = $this->domains->find('example-setup.org');

        $this->assertEquals('example.org example.com', $domain->aliases);

        $this->assertEquals('generic', $domain->template);

        $this->assertEquals('false', $domain->www);

        $this->assertEquals('custom', $domain->certificateType);

        $this->assertEquals('name@example-setup.org', $domain->certificateEmail);

        $this->assertEquals('8.0', $domain->engine);

        $this->assertEquals('example_db', $domain->databaseName);

        $this->assertEquals('deploy:all', $domain->deployCommand);

        $this->assertEquals('deploy:setup', $domain->deployInstall);

        $this->assertEquals('https://github.com/some/repository', $domain->gitUrl);

        $this->assertEquals('main', $domain->gitBranch);
    }

    public function wrongParameters(): array
    {
        return [
            ['www', 'xyz'],
            ['aliases', 'invalid domain'],
            ['template', 'none'],
            ['certificate.type', 'none'],
            ['certificate.email', 'invalid'],
            ['engine', '1.0'],
            ['database.name', 'example_db2'],
            ['database.user', 'user2'],
            ['git.url', 'fake_url'],
            ['git.branch', 'invalid branch'],
        ];
    }

    /**
     * @dataProvider wrongParameters
     * @throws Exception
     */
    public function test_domain_setup_wrong_parameters(string $key, string $value): void
    {
        $this->expectException(ValidationException::class);

        $this->artisan('domain:setup', [ 'name' => 'example-setup.org', 'key' => $key, 'value' => $value ])->assertFailed();
    }
}
