<?php

namespace Sculptor\Agent\Support;

use InvalidArgumentException;

class LookupResolver
{
    public static function array($app, string $key): array
    {
        $lookup = config($key);

        array_walk($lookup, fn (&$value, $key) => $value = $app->get($value));

        return $lookup;
    }

    public static function driver($app, string $drivers, string $key)
    {
        $driver = config($key);

        $lookup = config($drivers);

        if (array_key_exists($driver, $lookup)) {
            return $app->get($lookup[$driver]);
        }

        throw new InvalidArgumentException("Invalid {$driver} rotation type");
    }

    public static function drivers(string $key, $app): void
    {
        foreach (config($key) as $factory => $config) {
            $app->when($factory)
                ->needs('$drivers')
                ->give((fn($app) => LookupResolver::array($app, $config)));
        }
    }
}
