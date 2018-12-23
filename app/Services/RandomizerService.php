<?php

namespace App\Services;

use App\Exceptions\NoLocationsAvailableException;
use App\Rom;
use App\Item;
use App\World;
use App\Location;
use App\Support\ItemCollection;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling the randomization of a World object.
 */
class RandomizerService
{
    /** @var string */
    const VERSION = '1.0.0';

    /**
     * Get a collection of the items deemed as Required for progression in the world.
     *
     * @return \App\Support\ItemCollection
     */
    public function requiredItems() : ItemCollection
    {
        $items = [];

        foreach (config('item.advancement') as $item_name => $count) {
            for ($i = 0; $i < $count; ++$i) {
                $items[] = Item::get($item_name);
            }
        }

        return (new ItemCollection($items))->shuffle();
    }

    /**
     * Get any other items that need placed in the world, which are not required for further
     * exploration.
     *
     * @return \App\Support\ItemCollection
     */
    public function otherItems() : ItemCollection
    {
        $items = [];

        foreach (config('item.junk') as $item_name => $count) {
            for ($i = 0; $i < $count; ++$i) {
                $items[] = Item::get($item_name);
            }
        }

        return (new ItemCollection($items))->shuffle();
    }

    /**
     * Randomize the world passed in, this will change the places where certain things are within
     * the world. This is destructive.
     *
     * @param \App\World $world World to randomize
     *
     * @throws \App\Exceptions\NoLocationsAvailableException if it is unable to place required items
     *
     * @return void
     */
    public function randomize(World $world) : void
    {
        // very first we shuffle the safes around
        $safes = $world->getLocations()->filter(function ($location) {
            return $location instanceof Location\SafeHint || $location instanceof Location\SafeItem;
        });
        $rooms = array_map(function ($location) {
            return $location->getRoom();
        }, $safes->all());
        $shuffledSafes = $safes->shuffle();
        $shuffledSafes->each(function ($location) use (&$rooms) {
            $location->setRoom(array_pop($rooms));
        });
        // reset the edges
        $world->setItems(new ItemCollection);

        $placeItems = $this->requiredItems();
        $world->getLocation('Annie')->setItem(Item::get('Annie'));
        $emptyLocations = $world->getEmptyLocations()->shuffle();

        // Fill the things that are bound
        $emptyCages = $emptyLocations->filter(function ($location) {
            return $location instanceof Location\Cage;
        })->shuffle();

        $goonies = $placeItems->filter(function ($item) {
            return $item instanceof Item\Goonie;
        });

        // Shuffle Goonies around the World
        $cages = $world->getLocations()->filter(function ($location) {
            return $location instanceof Location\Cage;
        });
        $cagePlaces = $world->getPotentialGoonieRooms()->shuffle();
        $cages->each(function ($cage) use ($cagePlaces) {
            // we don't move Annie... just yet
            if ($cage->hasItem(Item::get('Annie'))) {
                return;
            }
            $cage->setRoom($cagePlaces->pop());
        });

        // place goonies in new cages
        foreach ($emptyCages as $cage) {
            $goonie = $goonies->pop();
            $cage->setItem($goonie);
            Log::debug(sprintf('Placing Item: %s in %s : %02x',
                $goonie->getNiceName(), $cage->getName(), $cage->getRoom()->getRoomId()));
        }

        $emptyHintSafes = $emptyLocations->filter(function ($location) {
            return $location instanceof Location\SafeHint;
        });

        $hints = $placeItems->filter(function ($item) {
            return $item instanceof Item\Hint;
        });

        foreach ($emptyHintSafes as $safe) {
            $hint = $hints->pop();
            $safe->setItem($hint);
            Log::debug(sprintf('Placing Item: %s in %s : %02x',
                $hint->getNiceName(), $safe->getName(), $safe->getRoom()->getRoomId()));
        }

        $toPlaceItems = $placeItems->filter(function ($item) {
            return !$item instanceof Item\Hint
                && !$item instanceof Item\Goonie;
        })->all();

        $start = $world->getVertex('Start');

        foreach ($toPlaceItems as $key => $item) {
            $placeItems->pull($key);
            $world->setItems($placeItems);

            $emptyLocations = $world->getReachableLocationsFrom($start)->getEmptyLocations()->shuffle();

            if ($emptyLocations->count() == 0) {
                throw new NoLocationsAvailableException(sprintf('No Available Locations: "%s"', $item->getNiceName()));
            }

            $fillLocation = $emptyLocations->first();

            Log::debug(sprintf('Placing Item: %s in %s : %02x',
                $item->getNiceName(), $fillLocation->getName(), $fillLocation->getRoom()->getRoomId()));

            $fillLocation->setItem($item);
        }

        // fast fill trash
        $locations = $world->getEmptyLocations();
        $fill_items = $this->otherItems()->all();

        foreach ($locations as $location) {
            $item = array_pop($fill_items);
            if (!$item) {
                break;
            }
            Log::debug(sprintf('Placing: %s in %s : %02x',
                $item->getNiceName(), $location->getName(), $location->getRoom()->getRoomId()));
            $location->setItem($item);
        }

        Log::debug(sprintf('Extra Items: %d', count($fill_items)));

        $locations = $world->getEmptyLocations();
        Log::debug(sprintf('Empty Locations: %d', $locations->count()));

        foreach ($locations as $location) {
            $location->setItem(Item::get('Nothing'));
        }
    }

    /**
     * Get a spoiler for this randomization.
     *
     * @todo implement
     *
     * @return array
     */
    public function getSpoiler() : array
    {
        return [];
    }

    /**
     * Prep and write world to a rom.
     *
     * @param \App\World $world World to write
     * @param \App\Rom   $rom   Rom to write to
     *
     * @return void
     */
    public function writeToRom(World $world, Rom $rom) : void
    {
        $rom->clearItemsFromRooms();

        $world->getLocations()->each(function ($location) use ($rom) {
            $location->writeItem($rom);
        });
    }
}
