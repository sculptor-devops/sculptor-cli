<?php

namespace Sculptor\Agent\Actions\Alarms;

use Exception;
use Sculptor\Agent\Actions\Alarms\Factories\Conditions;
use Sculptor\Agent\Actions\Alarms\Factories\Methods;
use Sculptor\Agent\Actions\Alarms\Factories\Rearms;
use Sculptor\Agent\Actions\Alarms\Factories\Subjects;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Repositories\Alarms;
use Sculptor\Agent\Repositories\Entities\Alarm;
use Sculptor\Agent\Validation\Validator;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Setup
{
    public function __construct(private Alarms $alarms, private Methods $methods, private Conditions $conditions, private Rearms $rearms, private Subjects $subjects)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name, string $key, string $value): void
    {
        $alarm = $this->alarms->find($name);

        if ($this->parameters($alarm, $key, $value)) {

            $alarm->save();

            return;
        }

        Validator::make('Alarm')->validateKeyValue($key, $value);

        $alarm->save(["$key" => "$value"]);

        if ($key == 'name') {
            $this->alarms->rename($name, $value);
        }
    }

    private function split(string $parameters): array
    {
        return Parameters::parse($parameters)->toArray();
    }

    /**
     * @throws Exception
     */
    private function parameters(Alarm $alarm, string $key, string $value): bool
    {
        switch ($key) {
            case 'action.parameters':
                $action = $this->methods->make($alarm->actionMethod);

                $action->parameters(Parameters::parse($value));

                $alarm->actionParametersArray = $this->split($value);

                return true;

            case 'condition.parameters':
                $condition= $this->conditions->make($alarm->conditionMethod);

                $condition->parameters(Parameters::parse($value));

                $alarm->conditionParametersArray = $this->split($value);

                return true;

            case 'subject.parameters':
                $subject = $this->subjects->make($alarm->subjectMethod);

                $subject->parameters(Parameters::parse($value));

                $alarm->subjectParametersArray = $this->split($value);

                return true;

            case 'rearm.parameters':
                $rearm = $this->rearms->make($alarm->rearmMethod);

                $rearm->parameters(Parameters::parse($value));

                $alarm->rearmParametersArray = $this->split($value);

                return true;
        }

        return false;
    }
}
