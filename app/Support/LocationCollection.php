<?php

namespace App\Support;

use App\Item;
use App\Location;

/**
 * Collection class meant only for Location objects.
 */
class LocationCollection extends Collection
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
        foreach ($items as $location) {
            if (!$location instanceof Location) {
                throw new \Exception('Trying to add non Location to LocationCollection');
            }
            $this->items[$location->getName()] = $location;
        }
    }

    /**
     * Get a Collection of Locations that do not have Items assigned.
     *
     * @return static
     */
    public function getEmptyLocations(): self
    {
        return $this->filter(function ($location) {
            return !$location->hasItem();
        });
    }

    /**
     * Get a Collection of Locations that do have Items assigned.
     *
     * @return static
     */
    public function getNonEmptyLocations(): self
    {
        return $this->filter(function ($location) {
            return $location->hasItem();
        });
    }

    /**
     * Deterime if the Locations given has at least a particular amount of a
     * particular Item.
     *
     * @param \App\Item  $item  Item to search for
     * @param int  $count  the required minimum number of Items
     *
     * @return bool
     */
    public function itemInLocations(Item $item, int $count = 1): bool
    {
        foreach ($this->items as $location) {
            if ($location->hasItem($item)) {
                --$count;
            }
        }

        return $count < 1;
    }

    /**
     * Get all the Items assigned in this.
     *
     * @return \App\Support\ItemCollection
     */
    public function getItems(): ItemCollection
    {
        $items = [];

        foreach ($this->items as $location) {
            $item = $location->getItem();
            if ($item !== null) {
                $items[] = $item;
            }
        }

        return new ItemCollection($items);
    }

    /**
     * Get a new Collection of Locations that have (a particlar) Item assigned.
     *
     * @param \App\Item $item Item to search for
     *
     * @return static
     */
    public function locationsWithItem(Item $item): self
    {
        return $this->filter(function ($location) use ($item) {
            return $location->hasItem($item);
        });
    }
}
