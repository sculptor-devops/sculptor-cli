<?php

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Sculptor\Agent\Support;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class Filesystem extends File
{
    /**
     * @throws Exception
     */
    public static function makeDirectoryRecursive(string $name): void
    {
        if (static::exists($name)) {
            return;
        }

        if (!static::makeDirectory($name, 0755, true, true)) {
            throw new Exception("Error creating directory $name");
        }
    }

    /**
     * @throws Exception
     */
    public static function fromTemplateFile(string $name, string $destination, ?string $alternate = null): void
    {
        $content = static::templateFile("/templates/{$name}");

        if (static::exists("$alternate/{$name}")) {
            $content = static::get("$alternate/{$name}");
        }

        if (!static::put($destination, $content)) {
            throw new Exception("Error creating file {$destination}");
        }
    }

    public static function templateFile(string $name): string
    {
        return Storage::get($name);
    }

    /**
     * @throws Exception
     */
    public static function fromTemplateDirectory(string $name, string $destination, ?string $alternate = null): void
    {
        $prefix = 'templates/' . $name;

        $files = Storage::allFiles('/' . $prefix);

        if (static::exists($alternate . '/' . $name)) {
            $files = static::allFiles($alternate . '/' . $name);
        }

        foreach ($files as $item) {
            $content = static::templateFile($item);

            if (!static::exists($destination)) {
                static::makeDirectoryRecursive($destination);
            }

            $filename = basename($item);

            if (!static::put("$destination/{$filename}", $content)) {
                throw new Exception("Error creating file {$destination}");
            }
        }
    }

    public static function getFiles(string $path, string $ext = '*'): array
    {
        $files = Filesystem::files($path);

        return collect($files)->filter(function (SplFileInfo $item) use ($ext) {
            return $item->isFile() && ($item->getExtension() == $ext || $ext == '*');
        })->toArray();
    }

    public static function getJson(string $filename): ?array
    {
        if (!static::exists($filename)) {
            return null;
        }

        return json_decode(static::get($filename), true);
    }

    /**
     * @throws Exception
     */
    public static function link(string $target, string $link): void
    {
        if (static::exists($link)) {
            Filesystem::delete($link);
        }

        if (!static::exists($target)) {
            throw new Exception("File $target not linkable");
        }

        try {
            if (!symlink($target, $link)) {
                throw new Exception("Cannot create link $link");
            }
        } catch (Exception $ex) {
            throw new Exception("Cannot create link $target -> $link: {$ex->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    public static function touch(string $filename): void
    {
        if (!static::put($filename, time())) {
            throw new Exception("Cannot touch file $filename");
        }
    }

    public static function folders(string $folder): array
    {
        return collect(static::directories($folder))->map(fn($folder) => basename($folder))->toArray();
    }

    /**
     * @throws Exception
     */
    public static function putJson(string $filename, array $data): void
    {
        if (!static::put($filename, json_encode($data, JSON_PRETTY_PRINT))) {
            throw new Exception("Cannot write json file $filename");
        }
    }

    /**
     * @throws Exception
     */
    public static function putYml(string $filename, array $data): void
    {
        $content = Yaml::dump($data);

        if (!static::put($filename, $content)) {
            throw new Exception("Cannot write yaml file $filename");
        }
    }

    public static function deleteIfExists(string $filename)
    {
        if (static::exists($filename)) {
            static::delete($filename);
        }
    }

    public static function deleteDirectoryIfExists(mixed $folder)
    {
        if (static::exists($folder)) {
            static::deleteDirectory($folder);
        }
    }
}
