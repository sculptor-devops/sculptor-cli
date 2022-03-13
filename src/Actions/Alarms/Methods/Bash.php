<?php

namespace Sculptor\Agent\Actions\Alarms\Methods;

use Sculptor\Agent\Actions\Alarms\Contracts\Method;
use Sculptor\Agent\Actions\Alarms\Support\Context;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Actions\Alarms\Support\Validable;
use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Bash extends Validable implements Method
{
    private Runner $runner;

    public array $properties = [
        'command',
        'message'
    ];

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    public function exec(Context $context): int
    {
        $command = $this->parameters->get('command');

        $response = $this->runner
            ->env($context->env())
            ->run(explode(' ', $command));

        return $response->code();
    }

    public function name(): string
    {
        return 'bash';
    }

    function parameters(Parameters $parameters): Method
    {
        parent::parameters($parameters);

        return $this;
    }
}
