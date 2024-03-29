<?php

namespace Sculptor\Agent\Actions\Domains\Certificates;

use Exception;
use Sculptor\Agent\Actions\Contracts\Certificate;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Repositories\Entities\Domain;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Custom implements Certificate
{
    public function __construct()
    {
        //
    }

    public function name(): string
    {
        return CertificatesTypes::CUSTOM;
    }

    public function register(Domain $domain, int $days = 3650): void
    {
        // TODO: Implement register() method.
    }

    public function pre(Domain $domain): void
    {
        // TODO: Implement pre() method.
    }

    public function deploy(Domain $domain): void
    {
        // TODO: Implement deploy() method.
    }

    public function delete(Domain $domain): void
    {
        // TODO: Implement delete() method.
    }

    /**
     * @throws Exception
     */
    public function files(Domain $domain): array
    {
        return [
            'certificate' => $domain->certs("{$domain->name()}.crt"),
            'certificate.key' => $domain->certs("{$domain->name()}.key")
        ];
    }
}
