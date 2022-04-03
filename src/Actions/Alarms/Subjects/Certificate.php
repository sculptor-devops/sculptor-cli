<?php

namespace Sculptor\Agent\Actions\Alarms\Subjects;

use Exception;
use Sculptor\Agent\Actions\Alarms\Contracts\Subject;
use Sculptor\Agent\Actions\Alarms\Support\Parameters;
use Sculptor\Agent\Actions\Alarms\Support\Validable;
use Spatie\SslCertificate\SslCertificate;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class Certificate extends Validable implements Subject
{
    public array $properties = [
        'domain',
        'days'
    ];

    public function name(): string
    {
        return 'certificate';
    }

    /**
     * @throws Exception
     */
    function value(): float
    {
        $domain = $this->parameters->get('domain');

        $days = intval($this->parameters->get('days'));

        $certificate = SslCertificate::createForHostName($domain);

        return $certificate->isValid() && $certificate->expirationDate()->diffInDays() > $days;
    }

    function parameters(Parameters $parameters): Subject
    {
        parent::parameters($parameters);

        return $this;
    }
}
