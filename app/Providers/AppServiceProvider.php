<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Intonate\TinkerZero\TinkerZeroServiceProvider;

use Sculptor\Agent\Configuration;
use Sculptor\Agent\Logs\Facades\Logs as LogsFacade;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Support\Folders;
use Sculptor\Agent\Support\LookupResolver;
use Sculptor\Agent\Support\Password;
use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Database\MySql;
use Sculptor\Foundation\Runner\Runner as RunnerImplementation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws Exception
     */
    public function boot(Folders $folders)
    {
        $this->validators();

        $folders->all();

        $this->app->make(Configuration::class)->databasePassword();
    }

    /**
     * Register any application services.
     *
     * @return void
     * @throws Exception
     */
    public function register()
    {
        $this->app->bind(LogsFacade::class, Logs::class);

        $this->app->bind(Runner::class, RunnerImplementation::class);

        $this->app->bind(Database::class, MySql::class);

        $this->app->singleton(Folders::class,Folders::class);

        $this->app->singleton(Configuration::class, fn($app) => new Configuration($app->make(Folders::class), $app->make(Password::class)));

        foreach (config('sculptor.factories') as $factory => $config) {
            $this->app->when($factory)
                ->needs('$drivers')
                ->give((fn($app) => LookupResolver::array($app, $config)));
        }

        if (unreleased())    {
            $this->app->register(TinkerZeroServiceProvider::class);
        }
    }

    private function validators(): void
    {
        Validator::extend('fqdn', 'App\Rules\Fqdn');

        Validator::extend('vcs', 'App\Rules\Vcs');

        Validator::extend('resolvable', 'App\Rules\Resolvable');

        Validator::extend('cron', 'App\Rules\Cron');

        Validator::extend('engine', 'App\Rules\Engine');

        Validator::extend('database', 'App\Rules\Database');

        Validator::extend('database.users', 'App\Rules\DatabaseUsers');

        Validator::extend('directory.exists', 'App\Rules\DirectoryExists');
    }
}
