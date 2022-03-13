<?php

namespace App\Commands\Backups;

use Carbon\Carbon;
use Exception;
use Sculptor\Agent\Actions\Backups\Archive;
use Sculptor\Agent\Support\Command\Base;

class BackupArchive extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'backup:archive {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show backup archive';

    /**
     * Execute the console command.
     *
     * @param Archive $archive
     * @return int
     * @throws Exception
     */
    public function handle(Archive $archive): int
    {
        $name = $this->argument('name');

        $archives = $archive->all($name);

        $total = collect($archives)->sum(fn($item) => $item->size());

        $rows = collect($archives)->map(function ($item){
            return [
                'name' => $item->name(),
                'date' => Carbon::parse($item->timestamp()),
                'size' => byteToHumanReadable($item->size()),
            ];
        });

        $this->table(['Name', 'Timestamp', 'Size'], $rows);

        $this->info(count($rows) . ' archives, ' . byteToHumanReadable($total) . ' total');

        return 0;
    }
}
