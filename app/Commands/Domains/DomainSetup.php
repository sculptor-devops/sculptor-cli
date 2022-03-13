<?php


namespace App\Commands\Domains;


use Exception;
use Sculptor\Agent\Actions\Domains\Setup;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainSetup extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:setup {name} {key} {value} {--skip-prepare}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit domain parameters';

    /**
     * Execute the console command.
     *
     * @param Setup $setup
     * @return int
     * @throws Exception
     */
    public function handle(Setup $setup): int
    {
        $name = $this->argument('name');

        $key = $this->argument('key');

        $value = $this->argument('value');

        $skipPrepare = $this->option('skip-prepare');

        $this->startTask("{$name} set {$key} = {$value}..");

        $setup->run($name, $key, $value);

        $this->completeTask();

        if (!$skipPrepare) {
            $this->call('domain:prepare', ['name' => $name]);
        }

        return 0;
    }
}
