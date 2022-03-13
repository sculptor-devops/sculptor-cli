<?php


namespace App\Commands\Domains;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Sculptor\Agent\Actions\Domains\Security;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainSecurity extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:security {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check domain security issues';

    /**
     * Execute the console command.
     *
     * @param Security $security
     * @return int
     * @throws GuzzleException
     */
    public function handle(Security $security): int
    {
        $name = $this->argument('name');

        $this->startTask("Security check {$name}");

        $issues = $security->run($name);

        if (count($issues)) {
            $this->errorTask('Found ' . count($issues) . ' vulnerabilities.');

            return $this->print($issues);
        }

        return $this->completeTask();
    }

    private function print(array $issues): int
    {
        $tabled = (collect($issues)->map(function ($item, $key) {
            return [
                'package' => "{$key}@{$item['version']}",
                'time' => Carbon::parse($item['time'])->format('Y:m:d'),
                'cve' =>  collect($item['advisories'])->map(function ($item) {
                    return $this->empty($item['cve']);
                })->first(),
                'advisories' => collect($item['advisories'])->map(function ($item) {
                    return $item['link'];
                })->first(),
            ];
        }))->sortBy('time');

        $this->table(['Package', 'Time', 'CVE', 'Description'], $tabled);

        return 1;
    }
}
