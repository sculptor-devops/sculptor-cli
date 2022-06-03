<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Actions\Alarms\Factories\Subjects;
use Sculptor\Agent\Actions\Alarms\Support\Context;
use Sculptor\Agent\Actions\Backups\Archive;
use Sculptor\Agent\Repositories\Alarms;
use Sculptor\Agent\Repositories\Backups;
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
class AlarmContextTest extends TestCase
{
    use RandomHome;

    private Alarms $alarms;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->app->make(Subjects::class);

        $this->backups = $this->app->make(Backups::class);

        $this->archive = $this->app->make(Archive::class);

        $this->app->bind(Runner::class, RunnerDummy::class);

        $this->app->bind(MySqlInterface::class, MySql::class);

        $this->alarms = $this->app->make(Alarms::class);

        $this->etc('example-backup.org');
    }

    /**
     * @throws Exception
     */
    public function test_context(): void
    {
        $alarm = $this->alarms->create('test_alarm');

        $alarm->actionMessage = 'When {SUBJECT_NAME} is {CONDITION_CONDITION} then {CONDITION_THRESHOLD} then call {ACTION_URL}';

        $context = new Context($alarm);

        $this->assertEquals('When cpu.load is greater then 1 then call https://example.org/test', $context->message());

        $this->assertEquals([
            "SUBJECT_NAME" => "cpu.load",
            "CONDITION_CONDITION" => "greater",
            "CONDITION_THRESHOLD" => "1",
            "ACTION_URL" => "https://example.org/test",
            "MESSAGE" => "When cpu.load is greater then 1 then call https://example.org/test",
            'ACTION_VERB' => 'post'
        ], $context->env());
    }
}
