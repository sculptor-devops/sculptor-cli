<?php

namespace Sculptor\Agent\Actions\Contracts;

use Sculptor\Agent\Repositories\Entities\Domain as Entity;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Domain
{
    public function create(Entity $domain, array $options = null): array;

    public function delete(Entity $domain, array $options = null): array;

    public function prepare(Entity $domain, array $options = null): array;

    public function deploy(Entity $domain, array $options = null): array;
}
