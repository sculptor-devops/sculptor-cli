<?php

namespace Tests\Unit;

use InvalidArgumentException;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class ParametersTest extends TestCase
{
    public function test_parameters_values(): void
    {
        $parameters = new Parameters(['a' => 'test1', 'b' => 'test2']);

        $this->assertEquals(['a' => 'test1', 'b' => 'test2'], $parameters->toArray());

        $this->assertEquals('a:=test1; b:=test2', $parameters->toString());

        $this->assertEquals(['a', 'b'], $parameters->keys());

        $this->assertEquals('test1', $parameters->get('a'));

        $this->assertEquals('test2', $parameters->get('b'));

        $this->assertTrue($parameters->has('a'));

        $this->assertTrue($parameters->has('b'));

        $this->assertFalse($parameters->has('c'));
    }

    public function test_wrong_parameters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Parameters(['a', 'b']);
    }

    public function parameters(): array
    {
        return [
            ['a :=test1; b:=test2'],
            ['a:=test1; b:= test2'],
            ['a:= test1; b:= test2'],
            [' a:=test1; b:=test2 '],
            [' a := test1; b := test2 '],
            ['a:=test1; b:=test2'],
            ['a:=test1;b:=test2'],
        ];
    }

    /**
     * @dataProvider parameters
     */
    public function test_parse_parameters(string $value): void
    {
        $parameters = Parameters::parse($value);

        $this->assertEquals(['a', 'b'], $parameters->keys());

        $this->assertEquals('test1', $parameters->get('a'));

        $this->assertEquals('test2', $parameters->get('b'));

        $this->assertTrue($parameters->has('a'));

        $this->assertTrue($parameters->has('b'));

        $this->assertFalse($parameters->has('c'));
    }

    public function wrongParameters(): array
    {
        return [
            [';;'],
            ['a;b;'],
            ['a;b:=test2;'],
            ['a;b:=test2'],
            ['a:=test1;b;'],
            ['a:=test1;b'],
        ];
    }

    /**
     * @dataProvider wrongParameters
     */
    public function test_parse_wrong_parameters($value): void
    {
        $this->expectException(InvalidArgumentException::class);

        Parameters::parse($value);
    }

    public function test_parse_wrong_associative_parameters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Parameters::make([
            'a' => 'test1',
            'b' => ['c' => 'test2']
        ]);
    }
}
