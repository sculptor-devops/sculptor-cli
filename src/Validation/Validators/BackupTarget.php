<?php

namespace Sculptor\Agent\Validation\Validators;

use Sculptor\Agent\Repositories\Databases;
use Sculptor\Agent\Repositories\Domains;
use Sculptor\Agent\Validation\Contracts\ValidatorRule;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class BackupTarget implements ValidatorRule
{
    public function __construct(private Databases $databases, private Domains $domains)
    {
    }

    public function rule(): array
    {
        return [
            'target' => [
                'required',
                'max:255',
                'in:' . $this->join()
            ]
        ];
    }

    private function join(): string
    {

        $databases = collect($this->databases->all())->map(fn($database) => $database->name())->join(',');

        $domains = collect($this->domains->all())->map(fn($domain) => $domain->name())->join(',');

        return "system,$databases,$domains";
    }
}
