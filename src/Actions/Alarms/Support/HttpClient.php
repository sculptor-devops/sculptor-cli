<?php

namespace Sculptor\Agent\Actions\Alarms\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class HttpClient
{
    public function __construct(private Parameters $parameters)
    {
        //
    }

    public static function make(Parameters $parameters): HttpClient
    {
        return new HttpClient($parameters);
    }

    public function result(?Context $context = null): Response
    {
        $url = $this->parameters->get('url');

        $verb = $this->parameters->get('verb');

        $data = [];

        if ($context) {
            $data = $context->env();
        }

        $client = $this->client();

        return match ($verb) {
            'get' => $client->get($url),
            'post' => $client->post($url, $data),
            'put' => $client->put($url, $data),
            'delete' => $client->delete($url, $data),
            default => throw new InvalidArgumentException("Unknown http verb $verb")
        };
    }

    private function client(): PendingRequest
    {
        $verify = $this->has('verify', 'true');

        $accept = $this->has('accept', 'application/json');

        $timeout = $this->has('timeout', '10');

        $client = Http::timeout($timeout);

        if ($verify != 'true') {
            $client = $client->withoutVerifying();
        }

        if ($accept) {
            $client = $client->accept($accept);
        }

        return $client;
    }

    private function has(string $key, ?string $default = null): ?string
    {
        if ($this->parameters->has($key)) {
            return $this->parameters->get($key);
        }

        return $default;
    }
}
