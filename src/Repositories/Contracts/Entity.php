<?php

namespace Sculptor\Agent\Repositories\Contracts;

use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
interface Entity
{
    public function __construct(Configuration $configuration, YmlFile $content);

    public function name(): string;

    public function save(array $values = null): void;

    public function __get(string $name);

    public function __set(string $name, $value): void;
}
