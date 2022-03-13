<?php

namespace Sculptor\Agent\Actions\Daemons\Services;


use Sculptor\Agent\Actions\Contracts\Service;
use Sculptor\Agent\Enums\DaemonGroupType;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Php implements Service
{
    public function __construct(private string $version)
    {
        //
    }

    function name(): string
    {
        return "php{$this->version}-fpm";
    }

    function package(): string
    {
        return "php{$this->version}-fpm";
    }

    function group(): string
    {
        return DaemonGroupType::WEB;
    }
}
