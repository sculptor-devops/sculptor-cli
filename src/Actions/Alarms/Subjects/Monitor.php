<?php

namespace Sculptor\Agent\Actions\Alarms\Subjects;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use Sculptor\Agent\Actions\Alarms\Contracts\Subject;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Actions\Alarms\Support\Validable;
use Sculptor\Agent\Monitors\Collector;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Monitor extends Validable implements Subject
{
    public array $properties = [
        'name'
    ];

    public function __construct(private Collector $monitors)
    {
        //
    }

    public function name(): string
    {
        return 'monitor';
    }

    /**
     * @throws BindingResolutionException
     */
    function value(): float
    {
        $name = Str::of($this->parameters->get('name'))->lower() . '';

        return $this->monitors->find($name);
    }

    function parameters(Parameters $parameters): Subject
    {
        parent::parameters($parameters);

        return $this;
    }
}
