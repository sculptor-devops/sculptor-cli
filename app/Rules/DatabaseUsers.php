<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Sculptor\Agent\Repositories\Databases;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DatabaseUsers implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private Databases $databases)
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value): bool
    {
        foreach ($this->databases->all() as $database) {
            if ($database->has($value)) {
                return true;
            }
        }

        return false;
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
        return 'Database user does not exists';
    }
}
