<?php

namespace Sculptor\Agent\Actions\Alarms;

use Exception;
use Sculptor\Agent\Actions\Alarms\Factories\Conditions;
use Sculptor\Agent\Actions\Alarms\Factories\Methods;
use Sculptor\Agent\Actions\Alarms\Factories\Rearms;
use Sculptor\Agent\Actions\Alarms\Factories\Subjects;
use Sculptor\Agent\Actions\Alarms\Support\Context;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Repositories\Alarms;
use Sculptor\Agent\Support\Chronometer;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Check
{
    use Logging;

    private string $error = '';

    public function __construct(private Alarms $alarms, private Methods $methods, private Conditions $conditions, private Rearms $rearms, private Subjects $subjects)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function run(string $name, bool $dry): bool
    {
        // Find
        $alarm = $this->alarms->find($name);

        // check if runnable
        if (!$alarm->runnable()) {
            $this->debug($alarm, [], 'disabled');

            return $alarm->statusAlarmedBool;
        }

        try {
            $this->debug($alarm, [], "action {$alarm->actionParameters()->toString()}");
            $action = $this->methods->make($alarm->actionMethod)
                ->parameters($alarm->actionParameters());

            $this->debug($alarm, [], "condition {$alarm->conditionParameters()->toString()}");
            $condition = $this->conditions->make($alarm->conditionMethod)
                ->parameters($alarm->conditionParameters());

            $this->debug($alarm, [], "subject {$alarm->subjectParameters()->toString()}");
            $subject = $this->subjects->make($alarm->subjectMethod)
                ->parameters($alarm->subjectParameters());

            $this->debug($alarm, [], "rearm {$alarm->rearmParameters()->toString()}");
            $rearm = $this->rearms->make($alarm->rearmMethod)
                ->parameters($alarm->rearmParameters());

            // get value from subject
            $value = $subject->value();
            $this->debug($alarm, [], "value $value");

            // evaluate condition on conditions
            $alarmed = $condition->check($value, $alarm->subjectLast);
            $this->debug($alarm, [], "alarmed " . ($alarmed ? 'yes' : 'no'));

            $rearmed = $rearm->act($alarmed, $alarm->statusPreviousBool);
            $this->debug($alarm, [], "rearmed " . ($rearmed ? 'yes' : 'no'));

            //REARM
            if (!$dry && $rearmed) {
                // run action
                $context = new Context($alarm);

                $result = $action->exec($context);

                $alarm->statusPreviousBool = $alarm->statusAlarmedBool;

                $alarm->statusAlarmedBool = true;

                $alarm->save([
                    'status.at' => Chronometer::now(),
                    'status.last' => Chronometer::now(),
                    'subject.last' => $value
                ]);

                $this->info($alarm, [], "alarm action $result");

                $this->error = "alarm action $result";

                return true;
            }

            $alarm->save(['status.last' => Chronometer::now(), 'subject.last' => $value]);

            $this->debug($alarm, [], 'not alarmed');
        } catch (Exception $ex) {
            report($ex);

            $this->error = $ex->getMessage();

            $alarm->save(['error' => $ex->getMessage()]);

            $this->err($alarm, [], $ex->getMessage());

            throw $ex;
        }

        return false;
    }

    public function error(): string
    {
        return $this->error;
    }
}
