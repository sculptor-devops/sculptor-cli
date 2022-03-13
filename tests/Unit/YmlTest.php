<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\YmlFile;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class YmlTest extends TestCase
{
    private YmlFile $yml;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $fixture = file_get_contents(base_path('tests/Fixtures/yaml.yml'));

        Filesystem::shouldReceive('get')->once()->with($fixture)->andReturn($fixture);

        Filesystem::shouldReceive('put')->withAnyArgs()->andReturnTrue();

        $this->yml = new YmlFile($fixture);
    }

    /**
     * @throws Exception
     */
    public function test_yml_load(): void
    {
         $this->assertEquals( [
             'version',
             'configuration.string',
             'configuration.bool',
             'configuration.int',
             'configuration.array.a',
             'configuration.array.b'
         ], $this->yml->keys());
    }

    /**
     * @throws Exception
     */
    public function test_yml_version(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->yml->verify(2);
    }

    public function test_yml_getters(): void
    {
        $this->assertEquals('test', $this->yml->get('configuration.string'));

        $this->assertEquals(true, $this->yml->getBool('configuration.bool'));

        $this->assertEquals(10, $this->yml->getInt('configuration.int'));

        $this->assertEquals(['a' => 1, 'b' => 2], $this->yml->getArray('configuration.array'));
    }

    public function test_yml_setters(): void
    {
        $this->yml->set('configuration.string', 'changed');

        $this->yml->setBool('configuration.bool', false);

        $this->yml->setInt('configuration.int', 100);

        $this->yml->setArray('configuration.array', ['a' => 2, 'b' => 3, 'c' => 4]);

        $this->assertEquals('changed', $this->yml->get('configuration.string'));

        $this->assertEquals(false, $this->yml->getBool('configuration.bool'));

        $this->assertEquals(100, $this->yml->getInt('configuration.int'));

        $this->assertEquals(['a' => 2, 'b' => 3, 'c' => 4], $this->yml->getArray('configuration.array'));
    }


}
