<?php


namespace App\Commands\Domains;


use Exception;
use Sculptor\Agent\Actions\Domains\Workers;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainWorkers extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain:workers {name} {operation=show} {prefix?} {shell?} {count=1} {--skip-prepare}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit domain workers';

    /**
     * Execute the console command.
     *
     * @param Workers $workers
     * @return int
     * @throws Exception
     */
    public function handle(Workers $workers): int
    {
        $name = $this->argument('name');

        $operation = $this->argument('operation');

        $prefix = $this->argument('prefix');

        $command = $this->argument('shell');

        $count = $this->argument('count');

        $skipPrepare = $this->option('skip-prepare');

        switch ($operation) {
            case 'show':
                $this->table(['Index', 'Prefix', 'Command', 'Count'], $workers->all($name));

                return 0;

            case 'add':
                $this->startTask("Add worker {$name} prefix {$prefix} command {$command}..");

                $workers->run($name, $prefix, $command, $count);

                break;

            case 'delete':
                $this->startTask("Delete worker {$name} at {$prefix}..");

                $workers->delete($name, $prefix);

                break;
            default:
                throw new Exception("Invalid operation $operation");
        }

        if (!$skipPrepare) {
            $this->call('domain:prepare', ['name' => $name]);
        }

        return $this->completeTask();
    }
}
