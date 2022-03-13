<?php

namespace Sculptor\Agent\Validation\Validators;

use App\Rules\Database;
use App\Rules\Fqdn;
use Sculptor\Agent\Repositories\Databases;
use Sculptor\Agent\Validation\Contracts\ValidatorRule;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class DomainDatabaseName implements ValidatorRule
{
    public function __construct(private Databases $databases)
    {
        //
    }

    public function rule(): array
    {
        return [
            'database_name' => [
                'required',
                'max:255',
                new Database($this->databases)
            ]
        ];
    }
}
