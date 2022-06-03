<?php

namespace Sculptor\Agent\Actions\Domains\Certificates;

use Exception;
use Sculptor\Agent\Actions\Contracts\Certificate;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Support\System;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class SelfSigned implements Certificate
{
    public function __construct(private System $system)
    {
        //
    }

    public function name(): string
    {
        return CertificatesTypes::SELF_SIGNED;
    }

    /**
     * @throws Exception
     */
    public function register(Domain $domain, int $days = 3650): void
    {
        $name = $domain->name();

        $path = $domain->certs();

        $this->system
            ->run(
                $path,
                [
                    'openssl',
                    'req',
                    '-new',
                    '-x509',
                    '-days',
                    $days,
                    '-nodes',
                    '-sha256',
                    '-out',
                    "{$path}/{$name}.crt",
                    '-keyout',
                    "{$path}/{$name}.key",
                    '-subj',
                    "/CN={$name}"
                ]
            );
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
