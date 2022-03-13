<?php

namespace Tests\Unit;

use InvalidArgumentException;
use Sculptor\Agent\Actions\Alarms\Conditions\Compare;
use Sculptor\Agent\Actions\Alarms\Conditions\Delta;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class ConditionsTest extends TestCase
{
    public function compareParameters(): array
    {
        return [
            [100, 0, 'greater', "10"],
            [100, 0, 'less', "1000"],
            [100, 0, 'equal', "100"],
            [100, 0, 'greaterequal', "100"],
            [100, 0, 'lessequal', "100"],
            [100, 0, 'different', "1"],

            [100, 0, 'gt', "10"],
            [100, 0, 'lt', "1000"],
            [100, 0, 'eq', "100"],
            [100, 0, 'gte', "100"],
            [100, 0, 'lte', "100"],
            [100, 0, 'dif', "1"],
        ];
    }

    /**
     * @dataProvider compareParameters
     */
    public function test_compare(float $current, float $last, string $condition, string $threshold): void
    {
        $compare = new Compare();

        $this->assertTrue($compare->parameters(Parameters::parse("condition:={$condition}; threshold:={$threshold}"))->check($current, $last));
    }

    public function test_compare_exception(): void
    {
        $compare = new Compare();

        $this->expectException(InvalidArgumentException::class);

        $compare->parameters(Parameters::parse("condition:=not_existent; threshold:=100"))->check(0, 0);
    }

    public function deltaParameters(): array
    {
        return [
            [120, 100, 'greater', "10"],
            [100, 150, 'less', "10"],
            [110, 100, 'equal', "10"],
            [120, 100, 'greaterequal', "20"],
            [120, 100, 'lessequal', "20"],
            [120, 100, 'different', "10"],

            [120, 100, 'gt', "10"],
            [100, 150, 'lt', "10"],
            [110, 100, 'eq', "10"],
            [120, 100, 'gte', "20"],
            [120, 100, 'lte', "20"],
            [120, 100, 'dif', "10"],

            [100, 120, 'less', "-10"],
            [100, 120, 'greater', "-20"],
            [100, 200, 'equal', "-50"],
        ];
    }

    /**
     * @dataProvider deltaParameters
     */
    public function test_delta(float $current, float $last, string $condition, string $threshold): void
    {
        $delta = new Delta();

        $this->assertTrue($delta->parameters(Parameters::parse("condition:={$condition}; threshold:={$threshold}"))->check($current, $last));
    }

    public function test_delta_exception(): void
    {
        $delta = new Delta();

        $this->expectException(InvalidArgumentException::class);

        $delta->parameters(Parameters::parse("condition:=not_existent; threshold:=100"))->check(100, 100);
    }
}
