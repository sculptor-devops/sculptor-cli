<?php

namespace Sculptor\Agent\Actions\Alarms\Support;

use Illuminate\Support\Str;
use Exception;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Converter
{
    public static function from(string $data): float
    {
        $value = filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $postfix = Str::of($data)->lower()->replace($value, '') . '';

        return match ($postfix) {
            'kb' => pow(1024, 1) * $value,
            'mb' => pow(1024, 2) * $value,
            'gb' => pow(1024, 3) * $value,
            'tb' => pow(1024, 4) * $value,

            'k' => pow(1000, 1) * $value,
            'm' => pow(1000, 2) * $value,
            'g' => pow(1000, 3) * $value,
            't' => pow(1000, 4) * $value,
            default => floatval($data)
        };
    }
}
