<?php

namespace Sculptor\Agent\Actions\Alarms\Subjects;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Sculptor\Agent\Actions\Alarms\Contracts\Subject;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Actions\Alarms\Support\Validable;
use Sculptor\Agent\Actions\Domains\Security as SecurityAction;

class Security extends Validable implements Subject
{
    public array $properties = [
        'name'
    ];

    public function __construct(private SecurityAction $security)
    {
        //
    }

    public function name(): string
    {
        return 'security';
    }

    /**
     * @throws Exception|GuzzleException
     */
    function value(): float
    {
        $name = Str::of($this->parameters->get('name'))->lower() . '';

        return count($this->security->run($name));
    }

    function parameters(Parameters $parameters): Subject
    {
        parent::parameters($parameters);

        return $this;
    }
}
