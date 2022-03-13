<?php

namespace Sculptor\Agent\Actions\Support;

use ReflectionClass;
use Sculptor\Agent\Logs\Facades\Logs;
use Sculptor\Agent\Logs\LogsContext;
use Sculptor\Agent\Repositories\Contracts\Entity;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
trait Logging
{
    private function log(Entity $entity): LogsContext
    {
        return Logs::actions(['name' => $entity->name()]);
    }

    public function message(?array $options, ?string $message = null): string
    {
        $options = $options ?? [];

        $reflect = new ReflectionClass($this);

        $parameters = '';

        if (count($options)) {
            $parameters = ' (' . join(', ', array_map(fn(string $value, string $key) => "$key=$value", $options, array_keys($options))) . ')';
        }

        $result = "{$reflect->getShortName()}";

        if ($message) {
            $result .= ": $message";
        }

        return $result . $parameters;
    }

    public function debug(Entity $entity, ?array $options = [], ?string $message = null): void
    {
        $this->log($entity)->debug($this->message($options, $message));
    }

    private function info(Entity $entity, ?array $options = [], ?string $message = null): void
    {
        $this->log($entity)->info($this->message($options, $message));
    }

    private function err(Entity $entity, ?array $options = [], ?string $message = null): void
    {
        $this->log($entity)->error($this->message($options, $message));
    }
}
