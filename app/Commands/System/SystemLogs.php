<?php

namespace App\Commands\System;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Logs\Local\Parser;
use Sculptor\Agent\Logs\Support\LogNameContext;
use Sculptor\Agent\Logs\Support\LogTagContext;
use Sculptor\Agent\Support\Command\Base;

class SystemLogs extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'system:logs {operation=show}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show system logs';

    /**
     * Execute the console command.
     *
     * @param Parser $logs
     * @return int
     * @throws Exception
     */
    public function handle(Parser $logs): int
    {
        $operation = $this->argument('operation');


        if ($operation == 'show') {
            $this->table(['Available files', 'Size'], $logs->files()->toArray());

            return 0;
        }

        $rows = $logs->file($operation);

        $this->table(['Level', 'Name', 'Tag', 'Date', 'Message', 'Stack'],
            $rows->map(function ($row) {
                return [
                    'level' => $row['level'],
                    'name' => new LogNameContext($row['context']),
                    'tag' => new LogTagContext($row['context']),
                    'date' => Carbon::parse($row['date']),
                    'text' => Str::limit($row['text'], 50),
                    'stack' => $this->noYes($row['stack'] == null)
                ];
            })->toArray());

        $this->warn("{$rows->count()} lines");

        return 0;
    }
}
