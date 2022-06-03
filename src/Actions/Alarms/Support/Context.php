<?php

namespace Sculptor\Agent\Actions\Alarms\Support;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

use Illuminate\Support\Str;
use Sculptor\Agent\Repositories\Entities\Alarm;
use Sculptor\Foundation\Support\Replacer;

class Context
{
    public function __construct(private Alarm $alarm)
    {
        //
    }

    public function attributes(): array
    {
        $result = [];

        foreach (
            [
                     'subject' => $this->alarm->subjectParameters(),
                     'rearm' => $this->alarm->rearmParameters(),
                     'condition' => $this->alarm->conditionParameters(),
                     'action' => $this->alarm->actionParameters()
                 ] as $name => $parameters
        ) {
            foreach ($parameters->keys() as $key) {
                $result["$name.$key"] = $parameters->get($key);
            }
        }

        return $result;
    }

    public function message(): string
    {
        $replaced = Replacer::make($this->alarm->actionMessage);

        $attributes = $this->attributes();

        foreach ($attributes as $key => $value) {
            $replaced->replace('{' . $this->normalize($key) . '}', $value);
        }

        return $replaced->value();
    }

    public function env(): array
    {
        $attributes = [];

        foreach ($this->attributes() as $key => $value) {
            $attributes[$this->normalize($key)] = $value;
        }

        return $attributes + [
            'MESSAGE' => $this->message()
        ];
    }

    private function normalize(string $key): string
    {
        return Str::of($key)->replace('.', '_')->upper();
    }
}
