<?php


namespace App\Commands\Domains;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Support\Filesystem;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainEnv extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:env {name} {key?} {value?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit a domain env template';

    /**
     * Execute the console command.
     *
     * @param Domains $domains
     * @return int
     * @throws Exception
     */
    public function handle(Domains $domains): int
    {
        $key = $this->argument('key');

        $value = $this->argument('value');

        $name = $this->argument('name');

        $domain = $domains->find($name);

        $template = $domain->configs('env');

        $content = Filesystem::get($template);

        $lines = preg_split("/\r\n|\n|\r/", $content);

        if ($key) {
            $this->startTask("$name $key=$value");

            $env = $this->replace($lines, $key, $value);

            Filesystem::put($template, $env);

            return $this->completeTask();
        }

        foreach ($lines as $line) {
            $this->info($line);
        }

        return 0;
    }

    private function replace(array $content, string $key, string $value): string
    {
        foreach ($content as &$line) {
            if (Str::of($line)->trim()->startsWith($key)) {
                $line = "$key=$value";
            }
        }

        return join("\n", $content);
    }
}
