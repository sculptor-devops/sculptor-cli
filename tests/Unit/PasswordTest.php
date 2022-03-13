<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Password;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class PasswordTest extends TestCase
{
    private Password $password;

    public function setUp(): void
    {
        parent::setUp();

        $this->password = $this->app->make(Password::class);
    }

    public function test_make_password(): void
    {
        $this->assertEquals(10, strlen($this->password->create(10)));

        $this->assertEquals(32, strlen($this->password->token()));
    }
}
