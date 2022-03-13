<?php

namespace App\Commands\System;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\BindingResolutionException;
use PhpParser\Node\Stmt\Switch_;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Monitors\Collector;
use Sculptor\Agent\Monitors\Formatter;
use Sculptor\Agent\Monitors\Writer;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Support\Filesystem;

class SystemMonitors extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'system:monitors {operation=show} {--raw}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Manipulate system monitors';

    public function __construct(private Configuration $configuration)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Collector $collector
     * @param Formatter $formatter
     * @param Writer $writer
     * @return int
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function handle(Collector $collector, Formatter $formatter, Writer $writer): int
    {
        $operation = $this->argument('operation');

        $raw = $this->option('raw');

        $all = $collector->all();

        $filename = $writer->filename();

        switch ($operation) {
            case 'show':
                $values = collect($all)->mapWithKeys(fn($item, $key) => [ $key => $raw ? $item : $formatter->value($key, $item) ]);

                $this->table(['Monitor', 'Value'], $this->toKeyValue($values->toArray()));

                if (Filesystem::exists($filename)) {
                    $this->info("Monitors stored in $filename (" . byteToHumanReadable(Filesystem::size($filename)) . ')');
                }

                return 0;

            case 'write':
                    $writer->append($all);

                    $this->info('Written ' . count($all) . ' records on ' . $writer->filename());
                return 0;

            case 'clear':
                if (Filesystem::exists($filename)) {
                    Filesystem::delete($filename);
                }

                $this->warn("Monitors file deleted");

                return 0;
        }

        throw new Exception("Invalid operation $operation");
    }

    public function schedule(Schedule $schedule): void
    {
        $cron = $this->configuration->get('monitors.cron') ?? '* * * * *';

        $schedule->command(static::class, ['write'])->cron($cron)->withoutOverlapping();
    }
}
