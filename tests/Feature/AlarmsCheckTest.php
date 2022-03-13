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
use function PHPUnit\Framework\assertEquals;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class AlarmsCheckTest extends TestCase
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

    /**
     * @throws Exception
     */
    public function test_alarm_check(): void
    {
        $name = 'test_alarm';

        $this->artisan('alarm:create', ['name' => $name])->assertSuccessful();

        foreach ([
                     'action.method' => 'webhook',
                     'action.parameters' => 'url:=https://example.org/test; verb:=post',
                     'subject.method' => 'monitor',
                     'subject.parameters' => 'name:=cpu.load',
                     'condition.method' => 'compare',
                     'condition.parameters' => 'condition:=greater; threshold:=0',
                     'rearm.method' => 'auto',
                 ] as $key => $value) {
            $this->artisan("alarm:setup", ['name' => $name, 'key' => $key, 'value' => $value])->assertSuccessful();
        }

        $this->artisan("alarm:check", ['name' => $name ])->assertFailed();

        $alarm = $this->alarms->find($name);

        $this->assertNotNull($alarm->statusAt);

        $this->assertNotNull($alarm->statusLast);

        $this->assertTrue($alarm->statusAlarmedBool);

        $this->assertFalse($alarm->statusPreviousBool);

        $this->assertGreaterThan(0, $alarm->subjectLast);
    }

    /**
     * @throws Exception
     */
    public function test_alarm_check_failure(): void
    {
        $name = 'test_alarm';

        $this->artisan("alarm:setup", ['name' => $name, 'key' => 'condition.parameters', 'value' => 'condition:=less; threshold:=0'])->assertSuccessful();

        $this->artisan("alarm:check", ['name' => $name ])->assertSuccessful();

        $alarm = $this->alarms->find($name);

        $this->assertNotNull($alarm->statusLast);

        $this->assertTrue($alarm->statusAlarmedBool);

        $this->assertFalse($alarm->statusPreviousBool);

        $this->assertGreaterThan(0, $alarm->subjectLast);
    }

    public function test_alarm_check_rearm(): void
    {
        $name = 'test_alarm';

        $this->artisan("alarm:setup", ['name' => $name, 'key' => 'rearm.method', 'value' => 'manual'])->assertSuccessful();

        $this->artisan("alarm:setup", ['name' => $name, 'key' => 'condition.parameters', 'value' => 'condition:=greater; threshold:=0'])->assertSuccessful();

        $this->artisan("alarm:check", ['name' => $name ])->assertFailed();

        $this->artisan("alarm:check", ['name' => $name ])->assertSuccessful();

        $this->artisan("alarm:rearm", ['name' => $name ])->assertSuccessful();

        $this->artisan("alarm:check", ['name' => $name ])->assertFailed();
    }
}
