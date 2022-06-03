<?php

namespace Sculptor\Agent\Actions\Alarms\Methods;

use Sculptor\Agent\Actions\Alarms\Contracts\Method;
use Sculptor\Agent\Actions\Alarms\Support\Context;
use Sculptor\Agent\Actions\Alarms\Support\HttpClient;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Actions\Alarms\Support\Validable;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Webhook extends Validable implements Method
{
    public array $properties = [
        'url',
        'verb'
    ];

    public function exec(Context $context): int
    {
        return HttpClient::make($this->parameters)->result($context)->status();
    }

    public function name(): string
    {
        return 'webhook';
    }

    public function parameters(Parameters $parameters): Method
    {
        parent::parameters($parameters);

        return $this;
    }
}
