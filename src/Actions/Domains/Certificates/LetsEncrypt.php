<?php

namespace Sculptor\Agent\Actions\Domains\Certificates;

use Exception;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use Sculptor\Agent\Actions\Contracts\Certificate;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Sculptor\Agent\Support\System;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class LetsEncrypt implements Certificate
{
    public function __construct(private System $system, private Folders $folders)
    {
        //
    }

    public function name(): string
    {
        return CertificatesTypes::LETS_ENCRYPT;
    }

    /**
     * @throws Exception
     */
    public function register(Domain $domain, int $days = 3650): void
    {
        $command = collect([
            'certbot',
            'certonly',
            '--webroot',
            '--agree-tos',
            '-n',

            '-m',
            $domain->certificateEmail,

            '--webroot-path',
            $domain->public(),

            '--deploy-hook',
            "/usr/bin/sculptor domain:certbot {$domain->name()} deploy",

            '--pre-hook',
            "/usr/bin/sculptor domain:certbot {$domain->name()} pre",

            '-d',
            $domain->name()
        ]);

        foreach (explode(' ', $domain->aliases) as $alias) {
            if (
                Str::of($alias)
                    ->trim()
                    ->isNotEmpty()
            ) {
                $command->push('-d')
                    ->push($alias);
            }
        }

        $this->system
            ->run($domain->root('certs'), $command->toArray());
    }

    public function pre(Domain $domain): void
    {
        // TODO: Implement pre() method.
    }

    /**
     * @throws Exception
     */
    public function deploy(Domain $domain): void
    {
        $this->copy($domain);
    }

    /**
     * @throws Exception
     */
    public function delete(Domain $domain): void
    {
        $this->system
            ->run($domain->root('certs'), [
                'certbot',
                'delete',
                '--cert-name',
                $domain->name()
            ]);
    }

    /**
     * @throws Exception
     */
    #[ArrayShape(['certificate' => "string", 'certificate.chain' => "string", 'certificate.full' => "string", 'certificate.key' => "string"])]
    public function files(Domain $domain): array
    {
        $path = "{$this->folders->etc()}/letsencrypt/live/{$domain->name()}";

        return [
            'certificate' =>  "$path/cert.pem",
            'certificate.chain' =>  "$path/chain.pem",
            'certificate.full' =>  "$path/fullchain.pem",
            'certificate.key' =>  "$path/privkey.pem",
        ];
    }

    /**
     * @param Domain $domain
     * @throws Exception
     */
    public function copy(Domain $domain): void
    {
        $path = $domain->certs();

        foreach ($this->files($domain) as $key => $file) {
            if (Filesystem::exists($file)) {
                Filesystem::copy($file, "$path/" . basename($file));
            }
        }
    }
}
