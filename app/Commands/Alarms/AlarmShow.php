<?php

namespace App\Commands\Alarms;


use Exception;
use Lorisleiva\CronTranslator\CronParsingException;
use Lorisleiva\CronTranslator\CronTranslator;
use Sculptor\Agent\Actions\Alarms\Parameters;
use Sculptor\Agent\Repositories\Alarms;
use Sculptor\Agent\Repositories\Entities\Alarm;
use Sculptor\Agent\Support\Command\Base;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class AlarmShow extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'alarm:show {name?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show alarms';

    /**
     * @throws Exception
     */
    public function handle(Alarms $alarms, Parameters $parameters): int
    {
        $name = $this->argument('name');

        if ($name) {
            $this->single($alarms->find($name));

            $this->parameters($parameters->all());

            return 0;
        }

        return  $this->all($alarms->all());
    }

    private function parameters(array $parameters): void
    {
        foreach ($parameters as $key => $value) {
            $this->info("Parameters for $key: {$this->empty($value)}");
        }
    }

    /**
     * @throws CronParsingException
     */
    public function single(Alarm $alarm): int
    {
        $this->table(['Name', 'Value'],
            [
                ['name' => 'enabled', 'Value' => $this->yesNo($alarm->enabled)],
                ['name' => 'name', 'Value' => $alarm->name()],
                ['name' => 'error', 'Value' => $this->empty($alarm->error)],
                ['name' => 'cron', 'Value' => CronTranslator::translate($alarm->cron)],

                ['name' => 'action.method', 'Value' => $alarm->actionMethod],
                ['name' => 'action.parameters', 'Value' => $this->empty($alarm->actionParameters()->toString())],

                ['name' => 'subject.method', 'Value' => $alarm->subjectMethod],
                ['name' => 'subject.parameters', 'Value' =>  $this->empty($alarm->subjectParameters()->toString())],
                ['name' => 'subject.last', 'Value' =>  $alarm->subjectLast],

                ['name' => 'condition.method', 'Value' => $alarm->conditionMethod],
                ['name' => 'condition.parameters', 'Value' => $this->empty($alarm->conditionParameters()->toString())],

                ['name' => 'rearm.method', 'Value' => $alarm->rearmMethod],
                ['name' => 'rearm.parameters', 'Value' => $this->empty($alarm->rearmParameters()->toString())],

                ['name' => 'status.alarmed', 'Value' => $this->noYes($alarm->statusAlarmed)],
                ['name' => 'status.previous', 'Value' => $this->noYes($alarm->statusPrevious)],
                ['name' => 'status.at', 'Value' => $this->empty($alarm->statusAt)],
                ['name' => 'status.last', 'Value' => $this->empty($alarm->statusLast)],
            ]
        );

        $this->warn("Name column indicate the key to use when change value with setup command,");
        $this->warn("Example: use alarm:setup {$alarm->name()} <<key>> <<value>>");
        $this->warn("For complex action/subject/condition/rearm.parameters you can use a <A>:=<B>; <C>:=<D> quoted string");

        return 0;
    }

    /**
     * @throws CronParsingException
     */
    public function all(array $all): int
    {
        $tabled = [];

        $count = count($all);

        foreach ($all as $alarm) {
            $tabled[] = [
                $this->yesNo($alarm->enabled),
                $alarm->name(),
                $this->noYes($alarm->statusAlarmed, 'On', 'Off'),
                $this->empty($alarm->statusAt),
                CronTranslator::translate($alarm->cron),
                $alarm->actionMethod,
                $this->empty($alarm->error),
            ];
        }

        $this->table(['Enabled', 'name', 'Status', 'At', 'Cron', 'Action', 'Error'], $tabled);

        $this->info("{$count} Alarms");

        return 0;
    }
}
