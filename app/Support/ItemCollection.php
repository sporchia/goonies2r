<?php

namespace App\Support;

use App\Item;

/**
 * Collection class meant only for Item objects.
 */
class ItemCollection extends Collection
{
    /**
     * Create a new collection.
     *
     * @param  mixed  $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
        $this->items = [];
        foreach ($items as $item) {
            if (!$item instanceof Item) {
                throw new \Exception('Trying to add non Item to ItemCollection');
            }
            $this->items[$item->getName()] = $item;
        }
    }

    /**
     * Determine if this collection has any keys in it.
     *
     * @return bool
     */
    public function hasKeys() : bool
    {
        return $this->has('KeysC0')
            || $this->has('KeysC1')
            || $this->has('KeysC2')
            || $this->has('KeysC3');
    }

    /**
     * Determine if this collection has any bombs in it.
     *
     * @return bool
     */
    public function hasBombs() : bool
    {
        return $this->has('BombsC4')
            || $this->has('BombsC5')
            || $this->has('BombsC6')
            || $this->has('BombsC7');
    }

    /**
     * {@inheritdoc}
     */
    public function shuffle($seed = null) : self
    {
        return parent::shuffle($seed);
    }
}
