<?php

namespace Tests\Fixtures;

use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Sculptor\Agent\Support\Password;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
trait DummyConfiguration
{
    private Configuration $configuration;

    private Folders $folders;

    public function configurationFileName(): string
    {
        return $this->folders->home() . '/configuration.yml';
    }

    public function configuration(string $filename = 'configuration.yml'): Configuration
    {




        $fixture = file_get_contents(base_path("tests/Fixtures/$filename"));

        $this->folders = $this->app->make(Folders::class);

        Filesystem::shouldReceive('get')->once()->with($this->configurationFileName())->andReturn($fixture);

        Filesystem::shouldReceive('exists')->once()->with($this->configurationFileName())->andReturnTrue();

        Filesystem::shouldReceive('put')->withSomeOfArgs($this->configurationFileName())->andReturnTrue();

        $this->app->singleton(Configuration::class, function($app) {
            return new Configuration($this->folders, $app->make(Password::class));
        });

        $this->configuration = $this->app->make(Configuration::class);

        return $this->configuration;
    }
}
