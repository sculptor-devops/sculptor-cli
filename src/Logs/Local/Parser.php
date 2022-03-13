<?php

namespace Sculptor\Agent\Logs\Local;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Symfony\Component\Finder\SplFileInfo;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Parser
{
    /**
     *
     */
    public const CONTEXT_PATTERN = '~\{(?:[^{}]|(?R))*\}~';

    /**
     * @var LaravelLogViewer
     */
    private LaravelLogViewer $parser;
    private Folders $folders;

    /**
     * Parser constructor.
     */
    public function __construct(Folders $folders)
    {
        $this->parser = new LaravelLogViewer();

        $this->folders = $folders;
    }

    /**
     * @param string $file
     * @return array
     * @throws Exception
     */
    private function rows(string $file): array
    {
        $this->parser->setFile($file);

        $parsed = $this->parser->all();

        foreach ($parsed as &$line) {
            $payload = null;

            if (preg_match(Parser::CONTEXT_PATTERN, $line['text'], $match) > 0) {
                $line['text'] = Str::of($line['text'])->replace($match[0], '')->trim() . '';

                $payload = $match;
            }

            $line['payload'] = $payload;
        }

        return $parsed;
    }

    /**
     * @return Collection
     * @throws Exception
     */
    public function files(): Collection
    {
        $path = $this->folders->logs();

        $files = Filesystem::getFiles($path, 'log');

        return collect($files)->map(function (SplFileInfo $file) {
            return [
                'name' => $file->getBasename(),
                'size' => byteToHumanReadable($file->getSize())
            ];
        });
    }

    /**
     * @param string|null $file
     * @return Collection
     * @throws Exception
     */
    public function file(string $file = null): Collection
    {
        if ($file == null) {
            $file = $this->files()->first();
        }

        $file = "{$this->folders->logs()}/{$file}";

        return collect($this->rows($file))
            ->map(function ($row) {
                return [
                    'level' => $row['level'],
                    'date' => Carbon::parse($row['date']),
                    'text' => $row['text'],
                    'stack' => $row['stack'],
                    'context' => $row['payload']
                ];
            });
    }
}
