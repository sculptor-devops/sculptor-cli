<?php

namespace Sculptor\Agent\Actions\Alarms\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Parameters
{
    public function __construct(private array $parameters)
    {
        if (!Arr::isAssoc($this->parameters) && count($parameters) > 0) {
            throw new InvalidArgumentException("Invalid parameters, must be key value array");
        }

        foreach ($this->keys() as $key) {
            $value = Arr::get($this->parameters, $key);
            if (!is_string($value) && !is_numeric($value)) {
                throw new InvalidArgumentException("Parameter $key is not a string or a number");
            }
        }
    }

    public static function make(array $parameters): Parameters
    {
        return new Parameters($parameters);
    }

    public static function parse(string $parameters, string $separator = ';', string $splitter = ':='): Parameters
    {
        $result = [];

        $parts = explode($separator, $parameters);

        if (count($parts) == 0 || Str::of($parameters)->isEmpty()) {
            return new Parameters($result);
        }

        foreach ($parts as $part) {
            $key = Str::of($part)->before($splitter)->trim() . '';

            $value = Str::of($part)->after($splitter)->trim() . '';

            if (!Str::of($part)->contains($splitter)) {
                throw new InvalidArgumentException("Error parsing $parameters, no separator found");
            }

            if (!$key) {
                throw new InvalidArgumentException("Error parsing $parameters, key empty");
            }

            if ($value == '' || $value == null) {
                throw new InvalidArgumentException("Error parsing $parameters, value empty on $key");
            }

            $result[$key] = $value;
        }

        return new Parameters($result);
    }

    public function toArray(): array
    {
        return $this->parameters;
    }

    public function has(string $key): bool
    {
        return Arr::has($this->parameters, $key);
    }

    public function get(string $key): string
    {
        return Arr::get($this->parameters, $key);
    }

    public function keys(): array
    {
        return collect($this->parameters)->keys()->toArray();
    }

    public function toString(string $separator = '; ', string $splitter = ':='): string
    {
        $result = '';

        foreach ($this->parameters as $key => $value) {
            $result .= ($result ? $separator : '') . "{$key}{$splitter}{$value}";
        }

        return $result;
    }
}
