<?php

namespace Sculptor\Agent\Support;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Upgrade
{
    private ?string $version = null;

    public function current(): string
    {
        return composerVersion();
    }

    /**
     * @throws Exception
     */
    public function online(): string
    {
        $updates = Http::get(UPDATES_URL);

        if ($this->version) {
            return $this->version;
        }

        if (!$updates->successful()) {
            throw new Exception("Http error returned status {$updates->status()}");
        }

        $payload = $updates->json();

        $version = collect(Arr::get($payload, 'packages.sculptor-devops/' . UPDATES_PACKAGE, []))->keys()->sort()->last();

        $version = Str::replaceFirst('v', '', $version);

        $this->version = $version;

        return $version;
    }

    /**
     * @throws Exception
     */
    public function available(): bool
    {
        return version_compare($this->online(), $this->current()) > 0;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $from = '/tmp/_sculptor_' . time();

        $to = '/bin/sculptor';

        $copy = copy(UPDATES_DOWNLOAD_URL, $from);

        if (!$copy) {
            throw new  Exception("Unable to download sculptor client");
        }

        if (!Filesystem::copy($from, $to)) {
            throw new  Exception("Unable to copy from $from to $to");
        }

        if (!Filesystem::chmod($to, 0755)) {
            throw new  Exception("Unable to chmod file $to");
        }
    }
}
