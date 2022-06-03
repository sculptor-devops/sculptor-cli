<?php

namespace Sculptor\Agent\Actions\Alarms\Subjects;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Actions\Alarms\Contracts\Subject;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Actions\Alarms\Support\Validable;
use Sculptor\Agent\Actions\Backups\Check;

class Backup extends Validable implements Subject
{
    public array $properties = [
        'name'
    ];

    public function __construct(private Check $check)
    {
        //
    }

    public function name(): string
    {
        return 'backup';
    }

    /**
     * @throws Exception
     */
    public function value(): float
    {
        $name = Str::of($this->parameters->get('name'))->lower() . '';

        return $this->check->run($name);
    }

    public function parameters(Parameters $parameters): Subject
    {
        parent::parameters($parameters);

        return $this;
    }
}
