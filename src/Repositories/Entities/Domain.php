<?php

namespace Sculptor\Agent\Repositories\Entities;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Exceptions\InvalidConfigurationException;
use Sculptor\Agent\Support\YmlFile;
use Sculptor\Agent\Repositories\Contracts\Entity as EntityInterface;
use Sculptor\Agent\Repositories\Support\Entity;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 * @property string $template
 * @property string $user
 * @property string $engine
 * @property string $aliases
 * @property string $certificateType
 * @property string $certificateEmail
 * @property string $status
 * @property array $crontabArray
 * @property array $workersArray
 * @property string $gitUrl
 * @property string $gitBranch
 * @property string $deployCommand
 * @property string $deployInstall
 * @property string $databaseName
 * @property string $databaseUser
 * @property bool $www
 * @property bool $enabled
 * @property string $created
 * @property string $deployed
 * @property string $public
 * @property string $token
 */
class Domain extends Entity implements EntityInterface
{
    protected array $fields = [
     "version",
     "enabled",
     "name",
     "aliases",
     "engine",
     "www",
     "user",
     "template",
     "status",
     "token",
     "created",
     "deployed",
     "database.name",
     "database.user",
     "certificate.type",
     "certificate.email",
     "git.url",
     "git.branch",
     "git.provider",
     "deploy.engine",
     "deploy.command",
     "deploy.install",
     //"crontab",
     //"workers",
   ];

    /**
     * @throws InvalidConfigurationException
     */
    public function __construct(Configuration $configuration, YmlFile $yml)
    {
        parent::__construct($configuration, $yml);

        $yml->verify(1);
    }

    /**
     * @throws Exception
     */
    private function compose(string $path, ?string $additional = null): string
    {
        $root = $this->root();

        if (!$additional) {
            return "$root/$path";
        }

        return "$root/$path/$additional";
    }

    /**
     * @throws Exception
     */
    public function configs(?string $filename = null): string
    {
        return $this->compose( 'configs', $filename);
    }

    /**
     * @throws Exception
     */
    public function bin(?string $filename = null): string
    {
        return $this->compose( 'bin', $filename);
    }

    /**
     * @throws Exception
     */
    public function logs(?string $filename = null): string
    {
        return $this->compose('logs', $filename);
    }

    /**
     * @throws Exception
     */
    public function shared(?string $filename = null): string
    {
        return $this->compose( 'shared', $filename);
    }

    /**
     * @throws Exception
     */
    public function public(?string $filename = null): string
    {
        $public = $this->public;

        if (!Str::of($public)->isEmpty()) {
            return $this->compose("current/$public", $filename);
        }

        return $this->compose('current', $filename);
    }

    /**
     * @throws Exception
     */
    public function certs(?string $filename = null): string
    {
        return $this->compose('certs', $filename);
    }

    /**
     * @throws Exception
     */
    public function root(?string $filename = null): string
    {
        if (!$filename) {
            return $this->configuration->root() . "/{$this->name()}";
        }

        return $this->configuration->root() . "/{$this->name()}/{$filename}";
    }

    /**
     * @throws Exception
     */
    public function current(?string $filename = null): string
    {
        return $this->compose('current', $filename);
    }

    public function serverNames(): string
    {
        $names = $this->name();

        if ($this->www) {
            $names = "{$names} www.{$names}";
        }

        if ($this->aliases) {
            $names = "{$names} {$this->aliases}";
        }

        return $names;
    }

    public function webhook(): string
    {
        $token = $this->token;

        $domain = $this->configuration->get('webhook');

        if (!$domain) {
            return '';
        }

        return "https://$domain/api/deploy/$token";
    }
}
