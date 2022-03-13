<?php

namespace Sculptor\Agent\Actions\Support;

use Exception;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Factory
{
    public function __construct(protected array $drivers)
    {
        //
    }

    public function keys(): array
    {
        return collect($this->drivers)
            ->map(fn($item) => $item->name())
            ->toArray();
    }

    /**
     * @throws Exception
     */
    protected function find(string $name)
    {
        foreach ($this->drivers as $driver) {
            if ($driver->name() == $name) {
                return $driver;
            }
        }

        throw new Exception("Invalid driver $name");
    }
}
