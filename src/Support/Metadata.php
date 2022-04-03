<?php


namespace Sculptor\Agent\Support;


use Sculptor\Agent\Support\Version\Composer;
use Sculptor\Agent\Support\Version\Php;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Metadata
{
    private array $content;

    public function __construct(Php $php, Composer $composer)
    {

    }

    public function from(string $filename): Metadata
    {
        return $this;
    }
}
