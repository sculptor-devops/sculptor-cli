<?php

namespace Sculptor\Agent\Monitors;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Writer
{
    public function __construct(private Configuration $configuration, private Folders $folders)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function filename(): string
    {
        $format = $this->configuration->get('monitors.format') ?? 'json';

        return "{$this->folders->home()}/monitors.{$format}";
    }

    public function samples(): int
    {
        return 3;// $this->configuration->get('monitors.samples') ?? 60;
    }

    /**
     * @throws Exception
     */
    public function append(array $data): void
    {
        $format = $this->configuration->get('monitors.format') ?? 'json';

        switch ($format) {
            case 'json':
                $this->json($data);

                return;

            case 'csv':
                $this->csv($data);

                return;
        }

        throw new Exception("Invalid monitor format $format");
    }

    /**
     * @throws Exception
     */
    public function json(array $data): void
    {
        $filename = $this->filename();

        $samples = $this->samples();

        $content = Filesystem::getJson($filename) ?? [];

        if (count($content) > $samples) {
            $content = collect($content)->skip(count($content) - $samples)->toArray();
        }

        $content[] = ['ts' => time() ] + $data;

        Filesystem::put($filename, json_encode($content, JSON_PRETTY_PRINT));
    }

    /**
     * @throws Exception
     */
    public function csv(array $data): void
    {
        $filename = $this->filename();

        $samples = $this->samples();

        $header = 'timestamp;' . join(';', collect($data)->keys()->toArray());

        if (!Filesystem::exists($filename)) {
            Filesystem::put($filename, '');
        }

        $content = Filesystem::get($filename);

        $content = preg_split("/\r\n|\n|\r/", $content);

        if (count($content) > $samples) {
            $skip = count($content) - $samples;

            $content = collect($content)->skip($skip);
        }

        $content = "{$header}\n" . $content->join("\n");

        $serialized = $content . time() . ';' . join(';', $data) . "\n";

        Filesystem::put($filename, $serialized);
    }
}
