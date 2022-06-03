<?php

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

if (!function_exists('whoami')) {

    function whoami(): string
    {
        $processUser = posix_getpwuid(posix_geteuid());

        return $processUser['name'];
    }
}

if (!function_exists('byteToHumanReadable')) {
    function byteToHumanReadable(int $size, int $precision = 2): string
    {
        $i = 0;
        $step = 1024;
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        return round($size, $precision) . $units[$i];
    }
}

if (!function_exists('composerVersion')) {
    function composerVersion(): ?string
    {
        $content = file_get_contents(__DIR__ . '/../composer.json');

        $payload = json_decode($content, true);

        if (array_key_exists('version', $payload)) {
            return $payload['version'];
        }

        return VERSION;
    }
}

if (!function_exists('testPrefix')) {
    /**
     * @throws Exception
     */
    function testPrefix(): string
    {
        return '_' . random_int(1000, 1000000);
    }
}

if (!function_exists('unreleased')) {
    function unreleased(): bool
    {
        return app()->version() == 'unreleased';
    }
}

if (!function_exists('isSudo')) {
    /**
     * @throws Exception
     */
    function isSudo(): bool
    {
        if (env('SCULPTOR_IGNORE_SUDO_CHECK') == 'true' || env('SCULPTOR_TEST') || unreleased()) {
            return true;
        }

        return posix_getuid() == 0;
    }
}

if (!function_exists('sameKeys')) {
    function sameKeys(array $a, array $b): bool
    {
        if (count($a) !== count($b)) {
            return false;
        }

        foreach ($b as $key => $bValue) {
            if (!in_array($bValue, $a, true)) {
                return false;
            }

            if (count(array_keys($a, $bValue, true)) !== count(array_keys($b, $bValue, true))) {
                return false;
            }
        }

        return true;
    }
}
