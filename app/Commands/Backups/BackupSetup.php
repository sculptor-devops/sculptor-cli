<?php

namespace App\Commands\Backups;

use Sculptor\Agent\Actions\Backups\Setup;
use Sculptor\Agent\Exceptions\ValidationException;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupSetup extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'backup:setup {name} {key} {value}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Setup backup';

    /**
     * Execute the console command.
     *
     * @param Setup $setup
     * @return int
     * @throws ValidationException
     */
    public function handle(Setup $setup): int
    {
        $name = $this->argument('name');

        $key = $this->argument('key');

        $value = $this->argument('value');

        $this->startTask("{$name} set {$key} = {$value}..");

        $setup->run($name, $key, $value);

        return $this->completeTask();
    }
}
