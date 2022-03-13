<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Actions\Alarms\Support\Converter;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class ConverterTest extends TestCase
{
    public function parameters(): array
    {
        return [
            [ '10G', 10_000_000_000 ],
            [ '10K', 10_000 ],
            [ '10M', 10_000_000 ],
            [ '10T', 10_000_000_000_000 ],
            [ '10KB', 10240 ],
            [ '10MB', 10485760 ],
            [ '10GB', 10737418240 ],
            [ '10TB', 10995116277760 ]
        ];
    }

    /**
     * @dataProvider parameters
     * @throws Exception
     */
    public function test_converter(string $data, float $expected): void
    {
        $value = Converter::from($data);

        $this->assertEquals($expected, $value, "Value $value but $expected expected");
    }
}
