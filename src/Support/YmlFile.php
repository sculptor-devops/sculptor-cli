<?php

namespace Sculptor\Agent\Support;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class YmlFile
{
    private string $filename;
    private array $content = [];
    protected array $hidden = [];
    protected array $masked = [];

    /**
     * @throws Exception
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;

        $content = Filesystem::get($filename);

        $this->content = Yaml::parse($content) ?? [];
    }

    private function mask(string $value): string
    {
        return Str::mask($value, '*', 0, strlen($value) - 4);
    }

    public function keys(): array
    {
        return collect(Arr::dot($this->content))
            ->reject(function ($value, $key) {
                return Str::startsWith($key, $this->hidden);
            })
            ->keys()->toArray();
    }

    public function all(): array
    {
        $all = [];

        foreach ($this->keys() as $key) {
            $value =  $this->get($key);

            if (in_array($key, $this->masked)) {
                $value = $this->mask($value);
            }

            $all[$key] = $value;
        }

        return $all;
    }

    public function get(string $name): string
    {
        return Arr::get($this->content, $name) ?? '';
    }

    public function getInt(string $name): int
    {
        $value = Arr::get($this->content, $name) ?? 0;

        return intval($value);
    }

    public function getBool(string $name): bool
    {
        $value = Arr::get($this->content, $name) ?? false;

        return $value == '1' || $value == 'true';
    }

    public function getArray(string $name): array
    {
        return Arr::get($this->content, $name) ?? [];
    }

    public function toArray(): array
    {
        return collect(Arr::dot($this->content))->reject(function ($value, $key) {
            return Str::startsWith($key, $this->hidden);
        })->mapWithKeys(function($value, $key) {
            if (in_array($key, $this->masked)) {
                return [ $key => $this->mask($value) ];
            }

            return [ $key => $value ];
        })->toArray();
    }

    public function set(string $key, string $value): YmlFile
    {
        Arr::set($this->content, $key, $value);

        return $this;
    }

    public function setBool(string $key, bool $value): YmlFile
    {
        Arr::set($this->content, $key, $value);

        return $this;
    }

    public function setInt(string $key, int $value): YmlFile
    {
        Arr::set($this->content, $key, $value);

        return $this;
    }

    public function setArray(string $key, array $value): YmlFile
    {
        Arr::set($this->content, $key, $value);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function save(array $values = null): void
    {
        if ($values) {
            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    $this->setArray($key, $value);

                    continue;
                }

                if (is_int($value)) {
                    $this->setInt($key, $value);

                    continue;
                }

                $this->set($key, $value);
            }
        }

        Filesystem::put($this->filename, Yaml::dump($this->content));
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function verify(int $min, int $max = null): void
    {
        $version = $this->getInt('version');

        if (!$max) {
            $max = $min;
        }

        if ($version > $max) {
            throw new InvalidConfigurationException("Invalid max configuration version {$version} found, {$max} expected");
        }

        if ($version < $min) {
            throw new InvalidConfigurationException("Invalid min configuration version {$version} found, {$min} expected");
        }
    }

    public function filename(): string
    {
        return $this->filename;
    }
}
