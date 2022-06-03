<?php

namespace Sculptor\Agent\Actions\Domains\Stages;

use Exception;
use Illuminate\Encryption\Encrypter;
use Sculptor\Agent\Actions\Contracts\Domain as DomainInterface;
use Sculptor\Agent\Actions\Domains\Support\Compiler;
use Sculptor\Agent\Actions\Support\Logging;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Databases;
use Sculptor\Agent\Repositories\Entities\Domain;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Foundation\Services\EnvParser;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Env implements DomainInterface
{
    use Logging;

    private array $files = [
        'env' => 'shared/.env',
        'ssh_config' => 'ssh_config'
    ];

    public function __construct(private Configuration $configuration, private Compiler $compiler, private Databases $databases)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function create(Domain $domain, array $options = null): array
    {
        throw new Exception("Deploy not implemented in env stage");
    }

    /**
     * @throws Exception
     */
    public function delete(Domain $domain, array $options = null): array
    {
        throw new Exception("Deploy not implemented in env stage");
    }

    /**
     * @throws Exception
     */
    public function prepare(Domain $domain, array $options = null): array
    {
        $this->debug($domain, $options, 'prepare');

        $database = [
            '{DATABASE}' => 'laravel',
            '{DATABASE_USERNAME}' => 'username',
            '{DATABASE_PASSWORD}' => 'secret',
            '{DATABASE_DRIVER}' => 'mysql',
            '{DATABASE_HOST}' => $this->configuration->get('database.host'),
            '{DATABASE_PORT}' => $this->configuration->get('database.port')
        ];

        foreach ($this->files as $filename => $destination) {
            $file = $domain->configs($filename);

            $content = $this->compiler->load($file, $domain->template);

            $compiled = $this->compiler->domain($content, $domain);

            if ($domain->databaseName && $domain->databaseUser) {
                $db = $this->databases->find($domain->databaseName);

                $user = $db->user($domain->databaseUser);

                $database['{DATABASE}'] = $domain->databaseName;
                $database['{DATABASE_USERNAME}'] = $domain->databaseUser;
                $database['{DATABASE_PASSWORD}'] = $user->password;
                $database['{DATABASE_DRIVER}'] = $db->driver;
            }

            $compiled = $compiled->replaces($database);

            $compiled = $compiled->replace('{KEY}', $this->currentKey($domain));

            if (
                !$this->compiler
                    ->save($domain->root($destination), $compiled->value())
            ) {
                throw new Exception("Unable to save env {$destination}/{$filename}");
            }
        }

        return $options ?? [];
    }

    /**
     * @throws Exception
     */
    public function deploy(Domain $domain, array $options = null): array
    {
        throw new Exception("Deploy not implemented in env stage");
    }

    /**
     * @param Domain $domain
     * @return string
     * @throws Exception
     */
    private function currentKey(Domain $domain): string
    {
        $filename = $domain->current('.env');

        if (!Filesystem::exists($filename)) {
            return $this->newKey($domain);
        }

        $parser = new EnvParser($filename);

        return $parser->get("APP_KEY", false);
    }

    /**
     * @throws Exception
     */
    private function newKey(Domain $domain): string
    {
        $config = $domain->current('config/app.php');

        $cipher = config('app.cipher');

        if (Filesystem::exists($config)) {
            $app = include($config);

            $cipher = $app['cipher'];
        }

        return 'base64:' . base64_encode(Encrypter::generateKey($cipher));
    }
}
