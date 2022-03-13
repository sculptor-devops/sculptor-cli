<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Sculptor\Agent\Support\Version\Php;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Engine implements Rule
{
    public function __construct(private Php $versions)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $this->versions->installed($value);
    }

    public function validate($attribute, $value, $parameters, $validator): bool
    {
        return $this->passes($attribute, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Invalid engine version.';
    }
}
