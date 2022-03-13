<?php

namespace Sculptor\Agent\Support;

use Exception;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Folders
{
    /**
     * @throws Exception
     */
    public function home(): string
    {
        if (env('SCULPTOR_HOME')) {
            return env('SCULPTOR_HOME') . '/.sculptor';
        }

        return userhome('.sculptor');
    }

    /**
     * @throws Exception
     */
    public function etc(): string
    {
        if (env('SCULPTOR_ETC')) {
            return env('SCULPTOR_ETC');
        }

        return '/etc';
    }

    /**
     * @throws Exception
     */
    private function make(string $name): string
    {
        $path = "{$this->home()}{$name}";

        Filesystem::makeDirectoryRecursive($path);

        return $path;
    }

    /**
     * @throws Exception
     */
    public function configuration(bool $force = false): string
    {
        $path = $this->make('');

        if (!Filesystem::exists("{$path}/configuration.yml") || $force) {
            Filesystem::fromTemplateFile('configuration.yml', "{$path}/configuration.yml");
        }

        return $path;
    }

    /**
     * @throws Exception
     */
    public function logs(): string
    {
        return $this->make('/logs');
    }

    /**
     * @throws Exception
     */
    public function domains(): string
    {
        return $this->make('/domains');
    }

    /**
     * @throws Exception
     */
    public function databases(): string
    {
        return $this->make('/databases');
    }

    /**
     * @throws Exception
     */
    public function backups(): string
    {
        return $this->make('/backups');
    }

    /**
     * @throws Exception
     */
    public function alarms(): string
    {
        return $this->make('/alarms');
    }

    /**
     * @throws Exception
     */
    public function templates(bool $force = false): string
    {
        $path = $this->make('/templates');

        foreach (['laravel', 'generic'] as $template) {
            if (!Filesystem::exists("{$this->home()}/{$template}") || $force) {
                Filesystem::fromTemplateDirectory($template, $this->home());
            }
        }

        return $path;
    }

    /**
     * @throws Exception
     */
    public function customTemplates(): string
    {
        return $this->make('/custom-templates');
    }

    /**
     * @throws Exception
     */
    public function monitors(): string
    {
        return $this->make('/monitors');
    }

    /**
     * @throws Exception
     */
    public function all(): array
    {
        return [
            $this->configuration(),
            $this->logs(),
            $this->templates(),
            $this->customTemplates(),
            $this->domains(),
            $this->databases(),
            $this->backups(),
            $this->alarms(),
            $this->monitors()
        ];
    }
}
