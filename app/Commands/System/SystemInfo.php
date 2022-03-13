<?php

namespace App\Commands\System;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Command\Base;
use Sculptor\Agent\Support\Upgrade as UpgradeService;
use Sculptor\Agent\Support\Version\Node;
use Sculptor\Agent\Support\Version\Php;
use Sculptor\Foundation\Support\Version;

class SystemInfo extends Base
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'system:info {--version-only}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'System information';

    /**
     * Execute the console command.
     *
     * @param Configuration $configuration
     * @param Version $version
     * @param Php $php
     * @param Node $node
     * @param UpgradeService $upgrade
     * @return int
     * @throws Exception
     */
    public function handle(Configuration $configuration, Version $version, Php $php, Node $node, UpgradeService $upgrade): int
    {
        $current = composerVersion();

        if ($this->option('version-only')) {
            $this->warn($current);

            return 0;
        }

        $this->table([
            'Name',
            'Value'
        ],
            [
                ['Name', 'Sculptor Devops (CLI)'],
                ['Version', $current],
                ['HOME', userHome()],
                ['PHP', implode(', ', $php->available())],
                ['NODE', $this->empty($node->version())],
                ['DB', $configuration->get('database.default')],
                ['OS', "{$version->name()}"],
                ['Arch', "{$version->arch()} ({$version->bits()} bit)"],
            ]);

        if ($upgrade->available()) {
            $this->warn("New update available " . $upgrade->online());
        }

        return 0;
    }
}
