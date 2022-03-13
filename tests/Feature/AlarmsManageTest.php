<?php

namespace Feature;

use Exception;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Sculptor\Agent\Exceptions\ValidationException;
use Sculptor\Agent\Repositories\Alarms;
use Sculptor\Agent\Repositories\Backups;
use Sculptor\Agent\Support\Folders;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Database\MySql as MySqlInterface;
use Tests\Dummy\MySql;
use Tests\Dummy\RandomHome;
use Tests\Dummy\Runner as RunnerDummy;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class AlarmsManageTest extends TestCase
{
    use RandomHome;

    private Alarms $alarms;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->folders = $this->app->make(Folders::class);

        $this->app->bind(Runner::class, RunnerDummy::class);

        $this->app->bind(MySqlInterface::class, MySql::class);

        $this->alarms = $this->app->make(Alarms::class);
    }

    public function test_alarm_create(): void
    {
        $this->artisan('alarm:create', ['name' => 'test_alarm'])->assertSuccessful();
    }

    public function parameters(): array
    {
        return [
            ['enabled', '1'],
            ['cron', '* * * * *'],

            ['action.method', 'bash'],
            ['action.parameters', 'command:=ls;message:=test'],

            ['action.method', 'webhook'],
            ['action.parameters', 'url:=https://example.org/test?query=test;verb:=post'],

            ['condition.method', 'compare'],
            ['condition.parameters', 'threshold:=10; condition:=gt'],

            ['condition.method', 'delta'],
            ['condition.parameters', 'threshold:=1; condition:=lt'],

            ['subject.method', 'backup'],
            ['subject.parameters', 'name:=example_backup'],

            ['subject.method', 'monitor'],
            ['subject.parameters', 'name:=cpu.load'],

            ['subject.method', 'response_status'],
            ['subject.parameters', 'url:=https://example.org; verb:=get'],

            ['subject.method', 'response_time'],
            ['subject.parameters', 'url:=https://example.org; verb:=get'],

            ['rearm.method', 'auto'],
            ['rearm.parameters', ''],

            ['rearm.method', 'manual'],
            ['rearm.parameters', ''],

            ['name', 'test_alarm_one'],
        ];
    }

    /**
     * @dataProvider parameters
     * @throws Exception
     */
    public function test_alarm_setup(string $key, string $value): void
    {
        $name = 'test_alarm';

        $this->artisan("alarm:setup", ['name' => $name, 'key' => $key, 'value' => $value])->assertSuccessful();

        if ($key == 'name') {
            $name = $value;
        }

        $alarm = $this->alarms->find($name);

        if (Str::endsWith($key, '.parameters')) {
            $this->assertIsArray($alarm->getArray($key));
        } else {
            $this->assertEquals($value, $alarm->get($key));
        }
    }

    public function wrongParameters(): array
    {
        return [
            ['cron', 'wrong cron', ValidationException::class],

            ['action.method', 'wrong', ValidationException::class],
            ['action.parameters', 'none:=test', InvalidArgumentException::class],
            ['action.method', 'wrong', ValidationException::class],
            ['action.parameters', 'none:=test', InvalidArgumentException::class],

            ['condition.method', 'wrong', ValidationException::class],
            ['condition.parameters', 'none:=test', InvalidArgumentException::class],

            ['condition.method', 'wrong', ValidationException::class],
            ['condition.parameters', 'none:=test', InvalidArgumentException::class],

            ['subject.method', 'wrong', ValidationException::class],
            ['subject.parameters', 'none:=test', InvalidArgumentException::class],

            ['subject.method', 'wrong', ValidationException::class],
            ['subject.parameters', 'none:=test', InvalidArgumentException::class],

            ['subject.method', 'wrong', ValidationException::class],
            ['subject.parameters', 'none:=test', InvalidArgumentException::class],

            ['subject.method', 'wrong', ValidationException::class],
            ['subject.parameters', 'none:=test', InvalidArgumentException::class],

            ['rearm.method', 'wrong', ValidationException::class],
            ['rearm.parameters', 'none:=test', InvalidArgumentException::class],

            ['rearm.method', 'wrong', ValidationException::class],
            ['rearm.parameters', 'none:=test', InvalidArgumentException::class],

            ['name', 'test with spaces', ValidationException::class]
        ];
    }

    /**
     * @dataProvider wrongParameters
     * @throws Exception
     */
    public function test_alarm_wrong_setup(string $key, string $value, $exception): void
    {
        $name = 'test_alarm_one';

        $this->expectException($exception);

        $this->artisan("alarm:setup", ['name' => $name, 'key' => $key, 'value' => $value])->assertSuccessful();
    }

    /**
     * @throws Exception
     */
    public function test_alarm_rearm(): void
    {
        $alarm = $this->alarms->find('test_alarm_one');

        $alarm->statusAlarmedBool = true;

        $alarm->save();

        $this->artisan("alarm:rearm", ['name' => 'test_alarm_one']);

        $alarm = $this->alarms->find('test_alarm_one');

        $this->assertFalse($alarm->statusAlarmedBool);
    }

    /**
     * @throws Exception
     */
    public function test_alarm_delete(): void
    {
        $this->artisan("alarm:delete", ['name' => 'test_alarm_one']);

        $this->assertFalse($this->alarms->exists('example_backup'));
    }
}
