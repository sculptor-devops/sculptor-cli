<?php

namespace Sculptor\Agent\Actions\Daemons;

use Exception;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Sculptor\Agent\Actions\Support\Daemons;
use Sculptor\Agent\Logs\Facades\Logs;
use Sculptor\Foundation\Services\Daemons as Services;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Operation
{
    private Services $services;

    private Daemons $daemons;

    public function __construct(Services $services, Daemons $daemons)
    {
        $this->services = $services;

        $this->daemons = $daemons;
    }

    /**
     * @throws Exception
     */
    public function run(string $operation, string $name = null): bool
    {
        Logs::batch()->info("Service group $name $operation");

        $result = true;

        $name = Str::lower($name);

        if (!$this->daemons->valid($name)) {
            throw new InvalidArgumentException("Invalid daemon group {$name}");
        }

        foreach ($this->daemons->only([ $name ]) as $daemon) {
            $result = $result && $this->execute($operation, $daemon->name());
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function execute(string $operation, string $daemon): bool
    {
        Logs::batch()->debug("Service {$operation} $daemon");

        $result = match ($operation) {
            'start' => $this->services->start($daemon),
            'restart' => $this->services->restart($daemon),
            'stop' => $this->services->stop($daemon),
            'reload' => $this->services->reload($daemon),
            'enable' => $this->services->enable($daemon),
            'disable' => $this->services->disable($daemon),
            default => throw new Exception("Unknown daemon $daemon operation $operation")
        };

        if (!$result) {
            throw new Exception("Service error $operation on $daemon: {$this->services->error()}");
        }

        return true;
    }
}
