<?php

namespace Sculptor\Agent\Actions\Backups;

use Exception;
use Sculptor\Agent\Exceptions\ValidationException;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Validation\Validator;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Setup
{
    public function __construct(private Backups $backups)
    {
        //
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function run(string $name, string $key, string $value): void
    {
        $backup = $this->backups->find($name);

        Validator::make('Backup')->validateKeyValue($key, $value);

        $backup->save(["$key" => "$value" ]);

        if ($key == 'name') {
            $this->backups->rename($name, $value);
        }
    }
}
