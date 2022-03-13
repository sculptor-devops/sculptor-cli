<?php

namespace Sculptor\Agent\Logs\Upgrades;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Sculptor\Foundation\Services\Daemons;
use Sculptor\Agent\Logs\Upgrades\Upgrade;
/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Upgrades
{
    /**
     * @var Daemons
     */
    private Daemons $daemons;
    /**
     * @var string
     */
    private string $service = 'unattended-upgrades';
    /**
     * @var string
     */
    private string $startTag = 'Log started:';
    /**
     * @var string
     */
    private string $endTag = 'Log ended:';
    /**
     * @var string
     */
    private string $filename = '/var/log/unattended-upgrades/unattended-upgrades-dpkg.log';
    /**
     * @var ?Carbon
     */
    private ?Carbon $startDate = null;
    /**
     * @var ?Carbon
     */
    private ?Carbon $endDate = null;
    /**
     * @var bool
     */
    private bool $opened = false;

    /**
     * Upgrades constructor.
     * @param Daemons $daemons
     */
    public function __construct(Daemons $daemons)
    {
        $this->daemons = $daemons;
    }

    /**
     * @return array
     */
    private function lines(): array
    {
        if (!File::exists($this->filename)) {
            return [];
        }

        $content = File::get($this->filename);

        return splitNewLine($content);
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function filename(string $filename): Upgrades
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function events(): array
    {
        $result = [];

        $last = null;

        foreach ($this->lines() as $line) {
            $date = null;

            if (Str::startsWith($line, $this->startTag)) {
                $date = new Carbon(Str::after($line, ':'));
            }

            if ($date == null) {
                continue;
            }

            if ($last == null) {
                $last = $date;
            }

            if (!$last->isSameDay($date)) {
                $result[] = $date;

                $last = $date;
            }
        }

        return $result;
    }

    /**
     * @param Carbon|null $date
     * @return Upgrade
     * @throws Exception
     */
    public function parse(Carbon $date = null): Upgrade
    {
        $result = [];

        if ($date == null) {
            $date = now();
        }

        foreach ($this->lines() as $line) {
            if ($this->parseStart($line, $date)) {
                continue;
            }

            if ($this->parseEnd($line, $date)) {
                continue;
            }

            if ($this->opened && trim($line) != '') {
                $result[] = trim($line);
            }
        }

        return new Upgrade($result, $this->startDate, $this->endDate);
    }

    /**
     * @return Upgrade
     * @throws Exception
     */
    public function last(): Upgrade
    {
        $events = collect($this->events());

        return $this->parse($events->last());
    }

    /**
     * @param string $line
     * @param Carbon $date
     * @return bool
     * @throws Exception
     */
    private function parseStart(string $line, Carbon $date): bool
    {
        if (Str::startsWith($line, $this->startTag) && !$this->opened) {
            $now = new Carbon(Str::after($line, ':'));

            $this->opened = ($date->day == $now->day &&
                $date->month == $now->month &&
                $date->year == $now->year);

            $this->startDate = ($this->startDate == null && $this->opened) ? $now : $this->startDate;

            return true;
        }

        return false;
    }

    /**
     * @param string $line
     * @param Carbon $date
     * @return bool
     */
    private function parseEnd(string $line, Carbon $date): bool
    {
        $current = $date;

        if (Str::startsWith($line, $this->endTag) && $this->opened) {
            $current = new Carbon(Str::after($line, ':'));
        }

        if ($current->isSameDay($date)) {
            return false;
        }

        $this->opened = false;

        $this->endDate = new Carbon(Str::after($line, ':'));

        return true;
    }

    /**
     * @return bool
     */
    public function enable(): bool
    {
        return $this->daemons
            ->enable($this->service);
    }

    /**
     * @return bool
     */
    public function active(): bool
    {
        return $this->daemons
            ->active($this->service);
    }
}
