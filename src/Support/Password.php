<?php

namespace Sculptor\Agent\Support;

use Illuminate\Support\Str;
use Sculptor\Agent\Configuration;
use Sculptor\Foundation\Contracts\Runner;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Password
{
    /**
     * @var Runner
     */
    private Runner $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    public function create(int $min, int $max = null): string
    {
        $length = rand($min, $max ?? $min);

        $password = $this->runner
            ->runOrFail([
                'openssl',
                'rand',
                '-base64',
                $length
            ]);

        return Str::limit(trim(preg_replace('/\s+/', ' ', $password)), $length, '');
    }

    public function token(int $length = 32): string
    {
        $token = $this->create($length * 2);

        $token = preg_replace('/[^A-Za-z0-9 ]/', '', $token);

        return Str::limit($token, $length, '');
    }
}
