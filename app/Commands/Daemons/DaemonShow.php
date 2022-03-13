<?php

namespace App\Commands\Daemons;

use Illuminate\Support\Str;
use Sculptor\Agent\Actions\Daemons\Status;
use Sculptor\Agent\Support\Command\Base;

class DaemonShow extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'daemon:show';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show daemons information';

    /**
     * Execute the console command.
     *
     * @param Status $status
     * @return int
     */
    public function handle(Status $status): int
    {
        $tabled = collect($status->run())
            ->sortBy(fn($item) => $item['group'])
            ->map(function ($item) {
                return [
                    'name' => $item['name'],
                    'group' => Str::upper($item['group']),
                    'installed' => $this->yesNo($item['installed']),
                    'running' => $this->yesNo($item['active'])
                ];
            });

        $this->table(['Service', 'Group', 'Installed', 'Running'], $tabled);

        return 0;
    }
}
