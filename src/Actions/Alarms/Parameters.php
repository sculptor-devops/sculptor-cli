<?php


namespace Sculptor\Agent\Actions\Alarms;

use Sculptor\Agent\Actions\Alarms\Factories\Conditions;
use Sculptor\Agent\Actions\Alarms\Factories\Methods;
use Sculptor\Agent\Actions\Alarms\Factories\Rearms;
use Sculptor\Agent\Actions\Alarms\Factories\Subjects;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Parameters
{
    public function __construct(private Methods $methods, private Conditions $conditions, private Rearms $rearms, private Subjects $subjects)
    {
        //
    }

    public function all(): array
    {
        $result = [];

        foreach ([
                     'method' => $this->methods,
                     'condition' => $this->conditions,
                     'subject' => $this->subjects,
                     'rearm' => $this->rearms
                 ] as $name => $driver) {

            foreach ($driver->keys() as $key) {
                $resolved = $driver->make($key);

                $result["$name=$key"] = join(', ', $resolved->properties);
            }
        }

        return $result;
    }

}
