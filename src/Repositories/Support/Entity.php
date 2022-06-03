<?php

namespace Sculptor\Agent\Repositories\Support;

use Exception;
use Illuminate\Support\Str;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Repositories\Contracts\Entity as EntityInterface;
use Sculptor\Agent\Support\YmlFile;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Entity implements EntityInterface
{
    protected array $fields = [ ];

    protected Configuration $configuration;

    protected YmlFile $content;

    public function __construct(Configuration $configuration, YmlFile $content)
    {
        $this->configuration = $configuration;

        $this->content = $content;
    }

    public function name(): string
    {
        return Str::replaceLast('.yml', '', basename($this->content->filename()));
    }

    /**
     * @throws Exception
     */
    public function save(array $values = null): void
    {
        $this->content->save($values);
    }

    /**
     * @throws Exception
     */
    private function normalize(string $name): object
    {
        $pointed = Str::of($name)
            ->snake('.');

        $normalized = $pointed
            ->remove('.bool')
            ->remove('.int')
            ->remove('.array');

        $type = collect(explode('.', $pointed))->last();

        if ($type == $normalized) {
            $type = 'string';
        }

        if (!in_array("$normalized", $this->content->keys())) {
            // throw new InvalidArgumentException("Invalid entity field $normalized of type $type");
        }

        return (object)[
           'value' => $normalized,
           'type' => $type
        ];
    }

    /**
     * @throws Exception
     */
    public function __get(string $name)
    {
        $normalized = $this->normalize($name);

        if ($normalized->type == 'bool') {
            return $this->content->getBool($normalized->value);
        }

        if ($normalized->type == 'int') {
            return $this->content->getInt($normalized->value);
        }

        if ($normalized->type == 'array') {
            return $this->content->getArray($normalized->value);
        }

        return $this->content->get($normalized->value);
    }

    /**
     * @throws Exception
     */
    public function __set(string $name, $value): void
    {
        $normalized = $this->normalize($name);

        if ($normalized->type  == 'bool') {
            $this->content->setBool($normalized->value, $value);

            return;
        }

        if ($normalized->type  == 'int') {
            $this->content->setInt($normalized->value, $value);

            return;
        }

        if ($normalized->type  == 'array') {
            $this->content->setArray($normalized->value, $value);

            return;
        }

        $this->content->set($normalized->value, $value);
    }

    public function validate(): bool
    {
        if (count($this->fields) == 0) {
            return true;
        }

        foreach ($this->fields as $field) {
            if (!in_array($field, $this->content->keys())) {
                return false;
            }
        }

        return true;
    }

    public function filename(): string
    {
        return $this->content->filename();
    }

    public function missing(): array
    {
        return array_diff($this->fields, $this->content->keys());
    }

    public function all(): array
    {
        $result = [];

        $keys = $this->content->keys();

        foreach ($keys as $key) {
            $result[$key] = $this->content->get($key);
        }

        return $result;
    }

    public function update(string $key, ?string $value): void
    {
        $this->content->set($key, $value);
    }

    public function get(string $key): string
    {
        return $this->content->get($key);
    }

    public function getArray(string $key): array
    {
        return $this->content->getArray($key);
    }
}
