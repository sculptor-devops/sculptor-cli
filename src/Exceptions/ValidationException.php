<?php

namespace Sculptor\Agent\Exceptions;

use Exception;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class ValidationException extends Exception
{
    public function __construct(string $key, string $value, string $message)
    {
        parent::__construct("Invalid value $value for $key: $message");
    }
}
