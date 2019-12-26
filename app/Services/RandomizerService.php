<?php

namespace App\Services;

use App\Exceptions\NoLocationsAvailableException;
use App\Item;
use App\Location;
use App\Rom;
use App\Support\Collection;
use App\Support\ItemCollection;
use App\World;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling the randomization of a World object.
 */
class RandomizerService
{
    /** @var string */
    const VERSION = '3.0.0';

    /**
     * Get a collection of the items deemed as Required for progression in the
     * world.
     *
     * @return \App\Support\ItemCollection
     */
    public function requiredItems(): ItemCollection
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
     * Get any other items that need placed in the world, which are not required
     * for further exploration.
     *
     * @return \App\Support\ItemCollection
     */
    public function otherItems(): ItemCollection
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
     * Randomize the world passed in, this will change the places where certain
     * things are within the world. This is destructive.
     *
     * @param \App\World  $world  World to randomize
     *
     * @throws \App\Exceptions\NoLocationsAvailableException  if it is unable to
     * place required items
     *
     * @return void
     */
    public function randomize(World $world): void
    {
        // very first we shuffle the safes around
        if ($world->config('shuffleItems', true)) {
            $safes = $world->getLocations()->filter(function ($location) {
                return $location instanceof Location\SafeHint
                    || $location instanceof Location\SafeItem;
            });
            $rooms = array_map(function ($location) {
                return $location->getRoom();
            }, $safes->all());

            $shuffledSafes = $safes->shuffle();
            $shuffledSafes->each(function ($location) use (&$rooms) {
                $location->setRoom(array_pop($rooms));
            });
        }

        // reset the edges
        $world->setItems(new ItemCollection);

        $placeItems = $this->requiredItems();

        $this->shuffleAnnie($world);

        $this->shuffleGoonies($world);

        $this->shuffleHints($world, $placeItems);

        $this->shuffleItems($world, $placeItems);
    }

    /**
     * Shuffle Annie in the given world.
     *
     * @param \App\World  $world  World to randomize
     *
     * @return void
     */
    public function shuffleAnnie(World $world): void
    {
        $annieCage = $world->getLocation('Annie');
        $annieCage->setItem(Item::get('Annie'));

        if ($world->config('shuffleAnnie', false)) {
            $annieCage->getRoom()->setCage(false);
            // set list of rooms due to GFX limitations ATM.
            $newRoom = $world->getRoom((new Collection([
                // 'Room 00', // has Hammer
                'Room 04',
                'Room 09',
                'Room 0a',
                'Room 15',
                'Room 19',
                // 'Room 1d', // has Helmet
                'Room 2e',
                'Room 36',
                // 'Room 39', // has Keys
                // 'Room 3a', // has Rain Coat
                // 'Room 3c', // has Fire Box
                // 'Room 41', // has Diving Suit
                'Room 4f',
                // 'Room 5c', // Speed Shoes Alt
                'Room 62',
                // 'Room 6d', // Safe
                'Room 78',
                // 'Room 79', // has Hyper Shoes
                'Room 7b',
                'Room 7c',
                'Room 7d',
            ]))->shuffle()->first());
            Log::debug(sprintf('Setting Annie to Room %02x', $newRoom->getRoomId()));
            $annieCage->setRoom($newRoom);
        }

        $annieCage->getRoom()->setAnnie(true);
    }

    /**
     * Shuffle Goonies in the given world.
     *
     * @param \App\World  $world  World to randomize
     *
     * @return void
     */
    public function shuffleGoonies(World $world): void
    {
        $goonies = $this->requiredItems()->filter(function ($item) {
            return $item instanceof Item\Goonie;
        });

        // Shuffle Goonies around the World
        $cages = $world->getLocations()->filter(function ($location) {
            return $location instanceof Location\Cage
                && !$location->hasItem();
        });

        if ($world->config('shuffleGoonies', true)) {
            $cagePlaces = $world->getPotentialGoonieRooms()->shuffle();

            $cages->each(function ($cage) {
                $cageRoom = $cage->getRoom();
                $cageRoom->setCage(false);

                Log::debug(sprintf('Uncaging Room %02x', $cageRoom->getRoomId()));
            });

            $cages->each(function ($cage) use ($cagePlaces) {
                $cage->setRoom($cagePlaces->pop());
            });
        }

        // place goonies in new cages
        foreach ($cages as $cage) {
            $goonie = $goonies->pop();
            $cage->setItem($goonie);

            Log::debug(sprintf(
                'Placing Item: %s in "%s" : Room %02x',
                $goonie->getNiceName(),
                $cage->getName(),
                $cage->getRoom()->getRoomId()
            ));
        }
    }

    /**
     * Shuffle Hints in the given world.
     *
     * @param \App\World  $world  World to randomize
     * @param \App\Support\ItemCollection  $placeItems  All Items to place
     *
     * @return void
     */
    public function shuffleHints(World $world, ItemCollection $placeItems): void
    {
        $emptyHintSafes = $world->getEmptyLocations()->shuffle()->filter(function ($location) {
            return $location instanceof Location\SafeHint;
        });

        $hints = $placeItems->filter(function ($item) {
            return $item instanceof Item\Hint;
        });

        foreach ($emptyHintSafes as $safe) {
            $hint = $hints->pop();
            $safe->setItem($hint);
            Log::debug(sprintf(
                'Placing Item: %s in "%s" : Room %02x',
                $hint->getNiceName(),
                $safe->getName(),
                $safe->getRoom()->getRoomId()
            ));
        }
    }

    /**
     * Shuffle Items in the given world.
     *
     * @param \App\World  $world  World to randomize
     * @param \App\Support\ItemCollection  $placeItems  All Items to place
     *
     * @return void
     */
    public function shuffleItems(World $world, ItemCollection $placeItems): void
    {
        if (!$world->config('shuffleItems', true)) {
            Log::debug('Skipping Item Shuffle');

            // Vanilla locations
            $world->getLocation('Item 00')->setItem(Item::get('Hammer'));
            $world->getLocation('Item 01')->setItem(Item::get('KeysC0'));
            $world->getLocation('Item 03')->setItem(Item::get('Slingshot'));
            $world->getLocation('Item 05')->setItem(Item::get('DeviceF0'));
            $world->getLocation('Item 14')->setItem(Item::get('Boomerang'));
            $world->getLocation('Item 1d')->setItem(Item::get('Helmet'));
            $world->getLocation('Item 2a')->setItem(Item::get('DeviceF2'));
            $world->getLocation('Item 4a')->setItem(Item::get('JumpShoes'));
            $world->getLocation('Item 46')->setItem(Item::get('DeviceF3'));
            $world->getLocation('Item 57')->setItem(Item::get('DeviceF4'));
            $world->getLocation('Item 67')->setItem(Item::get('FireboxCB'));
            $world->getLocation('Item 6b')->setItem(Item::get('DeviceF5'));
            $world->getLocation('Item 79')->setItem(Item::get('HyperShoes'));
            $world->getLocation('Item 20')->setItem(Item::get('DeviceF1'));
            $world->getLocation('Item 21')->setItem(Item::get('KeysC2'));
            $world->getLocation('Item 07')->setItem(Item::get('BombsC4'));
            $world->getLocation('Item 0d')->setItem(Item::get('FireboxC9'));
            $world->getLocation('Item 0e')->setItem(Item::get('Transceiver'));
            $world->getLocation('Item 0f')->setItem(Item::get('Candle'));
            $world->getLocation('Item 25')->setItem(Item::get('BombsC5'));
            $world->getLocation('Item 39')->setItem(Item::get('KeysC3'));
            $world->getLocation('Item 3c')->setItem(Item::get('FireboxC8'));
            $world->getLocation('Item 45')->setItem(Item::get('BombsC6'));
            $world->getLocation('Item 47')->setItem(Item::get('KeysC1'));
            $world->getLocation('Item 4b')->setItem(Item::get('Ladder'));
            $world->getLocation('Item 4c')->setItem(Item::get('BombsC7'));
            $world->getLocation('Item 56')->setItem(Item::get('FireboxCA'));
            $world->getLocation('Item e9')->setItem(Item::get('DivingSuit'));
            $world->getLocation('Item e8')->setItem(Item::get('Vest'));
            $world->getLocation('Item e7')->setItem(Item::get('Glasses'));
            $world->getLocation('Item ea')->setItem(Item::get('HyperShoes'));
            $world->getLocation('Item ec')->setItem(Item::get('Raincoat'));

            return;
        }

        $toPlaceItems = $placeItems->filter(function ($item) {
            return !$item instanceof Item\Hint
                && !$item instanceof Item\Goonie;
        })->all();

        $start = $world->getVertex('Start');

        foreach ($toPlaceItems as $key => $item) {
            $placeItems->pull($key);
            $world->setItems($placeItems);

            $emptyLocations = $world->getReachableLocationsFrom($start)
                ->getEmptyLocations()->shuffle();

            if ($emptyLocations->count() == 0) {
                throw new NoLocationsAvailableException(sprintf(
                    'No Available Locations: "%s"',
                    $item->getNiceName()
                ));
            }

            $fillLocation = $emptyLocations->first();

            Log::debug(sprintf(
                'Placing Item: %s in "%s" : Room %02x',
                $item->getNiceName(),
                $fillLocation->getName(),
                $fillLocation->getRoom()->getRoomId()
            ));

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

            Log::debug(sprintf(
                'Placing: %s in "%s" : Room %02x',
                $item->getNiceName(),
                $location->getName(),
                $location->getRoom()->getRoomId()
            ));

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
     * @param \App\World  $world  World to create spoiler for
     *
     * @return array
     */
    public function getSpoiler(World $world): array
    {
        $spoiler = [
            'meta' => [
                'shuffleAnnie' => $world->config('shuffleAnnie', false),
                'shuffleItems' => $world->config('shuffleItems', true),
                'shuffleGoonies' => $world->config('shuffleGoonies', true),
            ],
            'locations' => [],
        ];

        /** @var \App\Location $location */
        foreach ($world->getLocations() as $location) {
            $roomName = sprintf('Room %02x', $location->getRoom()->getRoomId());
            $item = $location->getItem();
            $spoiler['locations'][$roomName] = $item ? $item->getNiceName() : 'Nothing';
        }

        return $spoiler;
    }

    /**
     * Prep and write world to a rom.
     *
     * @param \App\World  $world  World to write
     * @param \App\Rom  $rom  Rom to write to
     *
     * @return void
     */
    public function writeToRom(World $world, Rom $rom): void
    {
        $rom->clearItemsFromRooms();

        $world->getLocations()->each(function ($location) use ($rom, $world) {
            $location->writeItem($rom);

            // handle Goonie Locator Devices
            $item = $location->getItem();
            if ($item instanceof Item\Goonie && $item->getName() !== 'Annie') {
                $door = $world->getDoorsToRoom($location->getRoom())->first();

                if ($door) {
                    $rom->updateMapLocator(
                        $door->meta['side'] === 'front',
                        $item->getByte() - 0xe0,
                        $door->meta['map_address']
                    );
                }
            }
        });

        // update room layouts
        $world->getRooms()->each(function ($room) use ($rom) {
            $room->writeToRom($rom);
        });
    }
}
