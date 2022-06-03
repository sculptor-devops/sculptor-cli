<?php

namespace Sculptor\Agent\Repositories\Contracts;

use Exception;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Entities\Database;
use Sculptor\Agent\Support\Filesystem;
use Sculptor\Agent\Support\Folders;
use Sculptor\Agent\Support\Password;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
abstract class Repository
{
    abstract public function path(): string;

    abstract public function name(): string;

    abstract public function make(YmlFile $file);

    private function fileName(string $name): string
    {
        return $this->path() . "/$name.yml";
    }

    public function __construct(protected Configuration $configuration, protected Folders $folders, protected Password $password)
    {
        //
    }

    public function exists(string $name): bool
    {
        return Filesystem::exists($this->fileName($name));
    }

    /**
     * @throws Exception
     */
    public function create(string $name, array $fields = null)
    {
        $filename = $this->fileName($name);

        if ($this->exists($name)) {
            throw new Exception("Resource {$this->name()} $name already exists");
        }

        Filesystem::fromTemplateFile("{$this->name()}.yml", $filename);

        $yml = new YmlFile($filename);

        $yml->save($fields);

        return $this->make($yml);
    }

    /**
     * @throws Exception
     */
    public function find(string $name)
    {
        $filename = $this->fileName($name);

        if (!$this->exists($name)) {
            throw new Exception("Resource {$this->name()} $name not found");
        }

        $yml = new YmlFile($filename);

        return $this->make($yml);
    }

    /**
     * @throws Exception
     */
    public function all(): array
    {
        $result = [];

        foreach (Filesystem::getFiles($this->path(), 'yml') as $file) {
            $yml = new YmlFile($file);

            $result[] = $this->make($yml);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function delete(string $name): void
    {
        $filename = $this->fileName($name);

        if (!$this->exists($name)) {
            throw new Exception("Resource {$this->name()} $name not found");
        }

        if (!Filesystem::delete($filename)) {
            throw new Exception("Resource {$this->name()} deletion error $filename");
        }
    }

    /**
     * @throws Exception
     */
    public function rename(string $name, string $renamed): void
    {
        if (!Filesystem::move($this->fileName($name), $this->fileName($renamed))) {
            throw new Exception("Cannot move {$this->fileName($name)}");
        }
    }
}
