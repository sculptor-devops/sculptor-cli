<?php

namespace Tests\Unit;

use Exception;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Sculptor\Agent\Actions\Alarms\Factories\Subjects;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Support\Chronometer;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class SubjectsTest extends TestCase
{
    private Subjects $factory;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->app->make(Subjects::class);
    }

    public function compareParameters(): array
    {
        return [
            ['cpu.load'],
            ['uptime.ticks'],
        ];
    }

    /**
     * @dataProvider compareParameters
     * @throws Exception
     */
    public function test_monitor(string $name): void
    {
        $monitor = $this->factory->make('monitor');

        $value = $monitor->parameters(Parameters::parse("name:={$name}"))->value();

        $this->assertGreaterThan(0, $value);
    }

    /**
     * @throws Exception
     */
    public function test_monitor_wrong_parameters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $monitor = $this->factory->make('monitor');

        $monitor->parameters(Parameters::parse("none:=test"));
    }

    /**
     * @throws Exception
     */
    public function test_response_code(): void
    {
        $url = 'https://example.org/test/?query=string';

        Http::fake([$url => Http::response([])]);

        $monitor = $this->factory->make('response_status');

        $value = $monitor->parameters(Parameters::parse("url:=$url;verb:=get"))->value();

        $this->assertEquals(200, $value);

        $value = $monitor->parameters(Parameters::parse("url:=$url;verb:=post"))->value();

        $this->assertEquals(200, $value);
    }

    /**
     * @throws Exception
     */
    public function test_response_code_wrong_verb(): void
    {
        $url = 'https://example.org/test/?query=string';

        Http::fake([$url => Http::response([])]);

        $this->expectException(InvalidArgumentException::class);

        $monitor = $this->factory->make('response_status');

        $monitor->parameters(Parameters::parse("url:=$url;verb:=test"))->value();
    }

    /**
     * @throws Exception
     */
    public function test_response_time(): void
    {
        $url = 'https://example.org/test/?query=string';

        Chronometer::mock(now(), 100);

        Http::fake([
            $url => Http::response([]),
        ]);

        $monitor = $this->factory->make('response_time');

        $value = $monitor->parameters(Parameters::parse("url:=$url;verb:=get"))->value();

        $this->assertEquals(100, $value);
    }

    /**
     * @throws Exception
     */
    public function test_response_time_wrong_verb(): void
    {
        $url = 'https://example.org/test/?query=string';

        Http::fake([$url => Http::response([])]);

        $this->expectException(InvalidArgumentException::class);

        $monitor = $this->factory->make('response_time');

        $monitor->parameters(Parameters::parse("url:=$url;verb:=test"))->value();
    }
}
