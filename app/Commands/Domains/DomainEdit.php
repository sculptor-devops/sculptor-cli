<?php


namespace App\Commands\Domains;

use Exception;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Foundation\Contracts\Runner;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainEdit extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:edit {name} {file?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit a domain template';

    /**
     * Execute the console command.
     *
     * @param Runner $runner
     * @param Domains $domains
     * @return int
     * @throws Exception
     */
    public function handle(Runner $runner, Domains $domains): int
    {
        $file = $this->argument('file');

        $name = $this->argument('name');

        $domain = $domains->find($name);

        $files = collect(Filesystem::getFiles($domain->configs()))->map(fn($file) => basename($file));

        if (!$file) {
            $this->info("You can specify this template files " . $files->join(', '));

            return 0;
        }

        $template = $domain->configs($file);

        if (!Filesystem::exists($template))  {
            $this->error("File $template does not exits, available " . $files->join(', '));

            return 1;
        }

        $runner->tty()->runOrFail([
            'sensible-editor',

            $template
        ]);

        return 0;
    }
}
