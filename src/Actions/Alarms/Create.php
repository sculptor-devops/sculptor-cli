<?php

namespace Sculptor\Agent\Actions\Alarms;


use Exception;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Alarms;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Create
{
    use Logging;

    public function __construct(private Alarms $alarms)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name): void
    {
        $alarm = $this->alarms->create($name);

        $this->info($alarm, [], 'created');

        $alarm->save([
            'name' => $name
        ]);
    }
}
