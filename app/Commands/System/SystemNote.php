<?php

namespace App\Commands\System;

use Sculptor\Agent\Logs\Logs;
use Sculptor\Agent\Support\Command\Base;

class SystemNote extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'system:note {note} {level=notice}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add system a note on events';

    /**
     * Execute the console command.
     *
     * @param Logs $logs
     * @return int
     */
    public function handle(Logs $logs): int
    {
        $note = $this->argument('note');

        $level = $this->argument('level') ?? 'info';

        if (!$note) {
            $this->warn("USAGE: system:note <<NOTE>> <<info|notice|warning|error|alert|critical|emergency|debug>");
            return 1;
        }

        $this->startTask("Adding note level {$level}");

        $logs->cli()->{$level}($note);

        return $this->completeTask();
    }
}
