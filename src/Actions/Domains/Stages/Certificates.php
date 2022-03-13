<?php

namespace Sculptor\Agent\Actions\Domains\Stages;

use Exception;
use Illuminate\Support\Arr;
use Sculptor\Agent\Actions\Contracts\Certificate as CertificateInterface;
use Sculptor\Agent\Actions\Contracts\Domain as DomainInterface;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Logs\Facades\Logs;
use Sculptor\Agent\Repositories\Entities\Domain as Entity;
use Sculptor\Agent\Support\Filesystem;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Certificates implements DomainInterface
{
    use Logging;

    public function __construct(private array $drivers)
    {
        //
    }

    /**
     * @throws Exception
     */
    private function find(string $name): CertificateInterface
    {
        foreach ($this->drivers as $driver) {
            if ($driver->name() == $name) {
                return $driver;
            }
        }

        throw new Exception("Certificate driver $name not found");
    }

    /**
     * @throws Exception
     */
    public function create(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'create');

        $driver = $this->find(CertificatesTypes::SELF_SIGNED);

        $driver->register($domain);

        return $this->files($domain, $driver->files($domain), $options ?? []);
    }

    /**
     * @throws Exception
     */
    public function delete(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'delete');

        $driver = $this->find($domain->certificateType);

        $driver->delete($domain);

        return $this->files($domain, $driver->files($domain), $options ?? []);
    }

    /**
     * @throws Exception
     */
    public function prepare(Entity $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'prepare');

        $options = $options ?? [];

        $hook = Arr::get($options, 'certbot.hook');

        $driver = $this->find($domain->certificateType);

        Logs::batch()->debug("Running certificate $hook on {$domain->name()}: {$domain->certificateType}");

        switch ($hook) {
            case 'register':
                $driver->register($domain);
                break;

            case 'deploy':
                $driver->deploy($domain);
                break;

            case 'pre':
                $driver->pre($domain);
                break;

            default:
                throw new Exception("Invalid hook $hook");
        }

        return $this->files($domain, $driver->files($domain), $options ?? []);
    }

    /**
     * @throws Exception
     */
    public function deploy(Entity $domain, array $options = null): array
    {
        throw new Exception("Deploy not implemented in certificates stage");
    }

    private function exists(array $files): bool
    {
        $result = true;

        foreach ($files as $key => $file) {
            $result = $result && Filesystem::exists($file);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function files(Entity $domain, array $files, array $options): array
    {
        if (!$this->exists($files)) {
            $driver = $this->find(CertificatesTypes::SELF_SIGNED);

            $files = $driver->files($domain);
        }

        foreach ($files as $key => $file) {
            $options[$key] = $file;
        }

        return $options;
    }
}
