<?php

namespace Sculptor\Agent;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Sculptor\Agent\Support\Password;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Configuration extends YmlFile
{
    protected array $hidden = [
        'version',
        'database.password',
        'security.key',
    ];

    protected array $masked = [
        'backup.drivers.s3.key',
        'backup.drivers.s3.secret',
        'backup.drivers.dropbox.key'
    ];

    private Password $password;
    private Folders $folders;

    /**
     * @throws Exception
     */
    public function __construct(Folders $folders, Password $password)
    {
        $this->folders = $folders;

        $this->password = $password;

        $filename = $this->fileName();

        if (!Filesystem::exists($filename)) {
            throw new Exception("Configuration file {$filename} not found!");
        }

        parent::__construct($this->fileName());

        $this->verify(1);

        $this->init();
    }

    /**
     * @throws Exception
     */
    public function init(): void
    {
        if ($this->get('security.key') == null || $this->get('security.key') == 'change-me') {
            $token = $this->password->token();

            $this->set('security.key', $token)->save();
        }

        $this->set('root', $this->root());
    }

    /**
     * @throws Exception
     */
    public function fileName(): string
    {
        return $this->folders->home() . '/configuration.yml';
    }

    public function version(): int
    {
        return $this->getInt('version');
    }

    public function securityKey(): int
    {
        return $this->getInt('security.key');
    }

    /**
     * @throws Exception
     */
    public function root(): string
    {
        if (env('SCULPTOR_SITES')) {
            return env('SCULPTOR_SITES');
        }

        return $this->get('root');
    }

    public function password(): string
    {
        $min = $this->get('security.password.min');

        $max = $this->get('security.password.max');

        return $this->password->create($min, $max);
    }

    public function databasePassword(): ?string
    {
        $passwordFile = $this->get('database.password');

        $driver = $this->get('database.default');

        $config = config("sculptor.database.drivers.{$driver}");

        if (!Filesystem::exists($passwordFile)) {
            return null;
        }

        $password = Filesystem::get($passwordFile);

        $connection = Arr::set($config, 'password', $password);

        config(['database.connections.db_server' => $connection]);

        return $password;
    }

    /**
     * @throws Exception
     */
    public function webhookDatabase(): void
    {
        $domain = $this->get('webhook');

        config(['database.connections.sqlite.database' => "{$this->root()}/$domain/current/database/database.sqlite"]);
    }
}
