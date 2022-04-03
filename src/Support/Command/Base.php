<?php

namespace Sculptor\Agent\Support\Command;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Sculptor\Agent\Support\Chronometer;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Base extends Command
{
    private string $taskName;

    private Chronometer $timer;

    public const PAD = 30;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        if (!isSudo()) {
            throw new Exception("Cannot run with unprivileged user");
        }
    }

    public function startTask(string $name, string $loading = 'running...'): void
    {
        $this->taskName = $name;

        $this->output->write("$name: <comment>{$loading}</comment>");

        $this->timer = Chronometer::start();
    }

    public function completeTask(): int
    {
        $this->endTask(true);

        return 0;
    }

    public function infoTask(string $title, bool $error = false): int
    {
        $this->startTask($title);

        if (!$error) {
            return $this->errorTask();
        }

        return $this->completeTask();
    }

    public function errorTask(string $error = 'failed'): int
    {
        $this->endTask(false, $error);

        return 1;
    }

    public function endTask(bool $completed, string $error = 'failed'): void
    {
        $elapsed = $this->timer->stop();

        $message = "{$this->taskName}: <info>✔</info> ($elapsed)";

        if (!$completed) {
            $message = "{$this->taskName}: <error>{$error}</error>";
        }

        if ($this->output->isDecorated()) {
            $this->output->write("\x0D");
            $this->output->write("\x1B[2K");
            $this->output->writeln($message);

            return;
        }

        $this->output->writeln('');
        $this->output->writeln($message);
    }

    public function yesNo(?bool $check, string $yes = 'YES', string $no = 'NO'): string
    {
        return $check ? "<info>$yes</info>" : "<error>$no</error>";
    }

    public function noYes(?bool $check, string $yes = 'YES', string $no = 'NO'): string
    {
        return $check ? "<error>$yes</error>" : "<info>$no</info>";
    }

    public function empty(?string $check, string $none = 'None'): string
    {
        if (!$check || $check == '') {
            return "<comment>$none</comment>";
        }

        return $check;
    }

    public function toKeyValue(array $values): array
    {
        $result = [];

        foreach ($values as $key => $value) {
            $result[] = ['key' => $key, 'value' => $value];
        }

        return $result;
    }

    public function toKeyValueHeaders(Collection $values): array
    {
        return collect($values->first())->keys()->toArray();
    }

    public function padded(string $key, string $value, int $pad = Base::PAD): void
    {
        $this->info(Str::padRight($key, $pad) . ": <fg=white>{$value}</>");
    }

    public function askYesNo(string $question = 'Continue? (yes/no)', bool $force = false): bool
    {
        if ($force) {
            return true;
        }

        $result = $this->ask($question);

        return Str::lower($result) == 'y' || Str::lower($result) == 'yes';
    }

    public function hasArguments(): bool
    {
        foreach (collect($this->arguments())->forget('command') as $argument) {
            if ($argument != null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Define the command's schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    #[Pure]
    public function formatRowsShown(array $rows): array
    {
        $values = [];

        foreach ($rows as $row) {
            $values[] = $this->formatRowShown($row[0], $row[1], $row[2], $row[3]);
        }

        return $values;
    }

    #[Pure] #[ArrayShape(['name' => "string", 'value' => "string", 'readonly' => "string"])]
    public function formatRowShown(string $name, string $value, string $type, bool $readonly): array
    {
        $value = match ($type) {
            'yesNo' => $this->yesNo($value),
            'noYes' => $this->noYes($value),
            'empty' => $this->empty($value),
            default => $value
        };

        return ['name' => $name, 'value' => $value, 'readonly' => $this->noYes($readonly)];
    }
}
