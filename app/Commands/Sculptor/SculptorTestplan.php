<?php

namespace App\Commands\Sculptor;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class SculptorTestplan extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sculptor:testplan {--run} {--clean} {--ignore} {--setup} {--deploy} {--verify} {--all}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sculptor testpan';

    private array $domains = ['example.org', 'example.com', 'example.net'];

    /**
     * @throws Exception
     */
    public function handle(): int
    {
        $clean = $this->option('clean');
        $setup = $this->option('setup');
        $deploy = $this->option('deploy');
        $verify = $this->option('verify');
        $all = $this->option('all');

        $this->header();

        if ($clean || $all) {
            $this->clean();
        }

        if ($setup || $all) {
            $this->setup();
        }

        if ($deploy || $all) {
            $this->deploy();
        }

        if ($verify || $all) {
            $this->verify();
        }

        return 0;
    }

    /**
     * @throws Exception
     */
    private function header(): void
    {
        $this->info("Staring...");
        $this->code('system:info', []);
        $this->code('daemon:show', []);
    }

    private function verify(): void
    {
        foreach ($this->domains as $name) {
            $url = "https://$name/api/test";

            $this->info("GET $url");

            $response = Http::asJson()->withoutVerifying()->get($url);

            $this->warn("CODE {$response->status()}, " . json_encode($response->json()));
        }
    }

    /**
     * @throws Exception
     */
    private function deploy(): void
    {
        foreach ($this->domains as $name) {
            $this->code('domain:deploy', ['name' => $name]);
        }
    }

    /**
     * @throws Exception
     */
    private function setup(): void
    {
        foreach ($this->domains as $name) {
            $prefix = Str::replace('.', '_', $name);

            $this->code('database:create', ['name' => "db_$prefix"]);
            $this->code('database:user', ['database' => "db_$prefix", 'name' => "user_$prefix"]);

            $this->code('domain:create', ['name' => $name, '--force' => true],);
            $this->code('domain:prepare', ['name' => $name]);

            $this->code('domain:env', ['name' => $name, 'key' => 'QUEUE_CONNECTION', 'value' => 'redis']);
            $this->code('domain:setup', ['name' => $name, 'key' => 'database.name', 'value' => "db_$prefix"]);
            $this->code('domain:setup', ['name' => $name, 'key' => 'database.user', 'value' => "user_$prefix"]);
            $this->code('domain:setup', ['name' => $name, 'key' => 'engine', 'value' => $this->version($name)]);
            $this->code('domain:setup', ['name' => $name, 'key' => 'git.url', 'value' => "https://github.com/sculptor-devops/test-site"]);

            $this->code('domain:workers', ['name' => $name, 'operation' => 'add', 'prefix' => 'queue', 'shell' => '{PHP} {CURRENT}/artisan queue:work']);
        }
    }

    /**
     * @throws Exception
     */
    private function clean(): void
    {
        $this->info("Cleaning...");

        foreach ($this->domains as $name) {
            $prefix = Str::replace('.', '_', $name);

            $this->code('domain:delete', ['name' => $name]);

            $this->code('database:user_delete', ['database' => "db_$prefix", 'name' => "user_$prefix"]);

            $this->code('database:delete', ['name' => "db_$prefix"]);
        }
    }

    private function version(string $name): string
    {
        return match ($name) {
            'example.com' => '8.0',
            'example.org' => '8.1',
            'example.net' => '7.4'
        };
    }

    /**
     * @throws Exception
     */
    private function code(string $command, array $args): void
    {
        $description = "sculptor-cli $command " . join(' ', $args);

        if (!$this->option('run')) {
            $this->warn($description);

            return;
        }

        try {
            if ($this->call($command, $args) > 0) {
                throw new Exception("Error running $command");
            }
        } catch (Exception $ex) {
            $this->warn($description);

            if (!$this->option('ignore')) {
                throw $ex;
            }

            $this->error("Error: {$ex->getMessage()}");
        }
    }
}
