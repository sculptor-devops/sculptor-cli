<?php

namespace Sculptor\Agent\Support;

use Exception;
use Sculptor\Agent\Support\Version\Composer;
use Sculptor\Agent\Support\Version\Php;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Metadata
{
    private YmlFile $content;

    public function __construct(private Php $php, private Composer $composer)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function from(string $filename): Metadata
    {
        $this->content = new YmlFile($filename);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function parse(): Metadata
    {
        $version = $this->content->get('engine.version');

        if ($this->content->getInt('version') != 1) {
            throw new Exception("Invalid metadata version");
        }

        if (
            !$this->php->installed($version &&
            $this->php->installed($this->content->get('engine.type') == 'php'))
        ) {
            throw new Exception("PHP version $version not installed");
        }

        return $this;
    }
}
