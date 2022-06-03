<?php

namespace Sculptor\Agent\Actions\Alarms\Subjects;

use Exception;
use Sculptor\Agent\Actions\Alarms\Contracts\Subject;
use Sculptor\Agent\Actions\Alarms\Support\HttpClient;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Actions\Alarms\Support\Validable;
use Sculptor\Agent\Support\Chronometer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class ResponseTime extends Validable implements Subject
{
    public array $properties = [
        'url',
        'verb'
    ];

    public function name(): string
    {
        return 'response_time';
    }

    /**
     * @throws Exception
     */
    public function value(): float
    {
        $time = Chronometer::start();

        HttpClient::make($this->parameters)->result();

        return $time->elapsed();
    }

    public function parameters(Parameters $parameters): Subject
    {
        parent::parameters($parameters);

        return $this;
    }
}
