<?php

namespace Sculptor\Agent\Actions\Alarms\Subjects;

use Exception;
use Sculptor\Agent\Actions\Alarms\Contracts\Subject;
use Sculptor\Agent\Actions\Alarms\Support\HttpClient;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Actions\Alarms\Support\Validable;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class ResponseStatus extends Validable implements Subject
{
    public array $properties = [
        'url',
        'verb'
    ];

    public function name(): string
    {
        return 'response_status';
    }

    /**
     * @throws Exception
     */
    public function value(): float
    {
        return HttpClient::make($this->parameters)->result()->status();
    }

    public function parameters(Parameters $parameters): Subject
    {
        parent::parameters($parameters);

        return $this;
    }
}
