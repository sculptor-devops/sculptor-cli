<?php

namespace App\Commands\System;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;

class SystemShow extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'system:show {--reset}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show system configuration';

    /**
     * Execute the console command.
     *
     * @param Folders $folders
     * @param Configuration $configuration
     * @return int
     * @throws Exception
     */
    public function handle(Folders $folders, Configuration $configuration): int
    {
        $this->show($configuration, $folders);

        $this->info("Use system:setup set <<key>> <<value>> to change value");

        return 0;
    }

    /**
     * @throws Exception
     */
    private function show(Configuration $configuration, Folders $folders): void
    {
        $all = collect($configuration->toArray())->map(fn($item) => $this->empty($item))->toArray();

        $all['database'] = $this->yesNo($configuration->databasePassword() !== null, 'PRESENT', 'NOT PRESENT') . ' (readonly)';

        $all['templates.dir'] = $folders->templates() . ' (readonly)';

        $all['templates.list'] = join(', ', Filesystem::folders($folders->templates())) . ' (readonly)';

        $all['configuration.path'] = $configuration->fileName() . ' (readonly)';

        $this->table(['Name', 'Value'], $this->toKeyValue($all));
    }
}
