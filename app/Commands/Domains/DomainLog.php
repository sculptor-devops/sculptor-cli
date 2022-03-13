<?php


namespace App\Commands\Domains;


use Carbon\Carbon;
use Exception;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Support\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainLog extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:logs {name} {log?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show domain log';

    /**
     * @throws Exception
     */
    public function handle(Domains $domains): int
    {
        $name = $this->argument('name');

        $log = $this->argument('log');

        $domain = $domains->find($name);

        $path = $domain->logs();

        if ($log) {
            return $this->single($path, $log);
        }

        return $this->all($path);
    }

    public function all(string $path): int
    {
        $files = collect(Filesystem::getFiles($path))->map(function (SplFileInfo $item) {
            return ['name' => $item->getBasename(), 'updated' => Carbon::parse($item->getMTime()), 'size' => byteToHumanReadable($item->getSize())];
        });

        $this->table(['Name', 'Updated', 'Size'], $files->toArray());

        return 0;
    }

    public function single(string $path, string $log): int
    {
        $content = Filesystem::get("$path/$log");

        foreach (preg_split("/\r\n|\n|\r/", $content) as $line) {
            if ($line) {
                $this->info($line);
            }
        }

        return 0;
    }
}
