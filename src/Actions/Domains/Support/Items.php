<?php

namespace Sculptor\Agent\Actions\Domains\Support;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Items
{
    public function __construct(private array $items)
    {
        //
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public static function make(array $items): Items
    {
        return new Items($items);
    }

    public function all(): array
    {
        $result = [];

        foreach ($this->items as $item) {
            $result[] = array('id' => count($result)) + $item;
        }

        return $result;
    }

    public function delete(int $index): Items
    {
        $this->items = collect($this->items)->except($index)->toArray();

        return $this;
    }

    public function add(array $item): Items
    {
        $this->items[] = $item;

        return $this;
    }
}
