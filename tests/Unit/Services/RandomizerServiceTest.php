<?php

namespace Tests\Unit\Services;

use App\Item;
use App\Location;
use App\Services\RandomizerService;
use App\Support\ItemCollection;
use App\World;
use Tests\TestCase;

class RandomizerServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testSpoilerContainsMeta(): void
    {
        $randomizer = new RandomizerService;
        $world = new World(new ItemCollection);

        $this->assertArrayHasKey('meta', $randomizer->getSpoiler($world));
    }

    /**
     * @return void
     */
    public function testTryingToRandomizeWithNoEmptyLocationsFails(): void
    {
        $this->expectException(\Exception::class);

        $randomizer = new RandomizerService;
        $world = new World(new ItemCollection);
        $world->getEmptyLocations()->each(function ($location) {
            if ($location instanceof Location\SafeHint || $location instanceof Location\Cage) {
                return;
            }

            $location->setItem(new Item('GoonieNE', 0x01));
        });

        $randomizer->randomize($world);
    }

    /**
     * Being random, it's super unlikely that Annie would land in her vanilla
     * location over a series of runs.
     *
     * @covers App\Services\RandomizerService::shuffleAnnie
     *
     * @return void
     */
    public function testAnnieShuffle(): void
    {
        $randomizer = new RandomizerService;
        for ($i = 0; $i < 20; $i++) {
            $world = new World(new ItemCollection, [
                'shuffleAnnie' => true,
            ]);

            $randomizer->shuffleAnnie($world);

            if ($world->getLocation('Annie')->getRoom() !== $world->getRoom('Room 4f')) {
                $this->assertTrue(true);

                return;
            }
        }

        $this->assertTrue(false, 'Failed to move Annie after 20 attempts');
    }

    /**
     * @covers App\Services\RandomizerService::shuffleItems
     *
     * @dataProvider vanillaProvider
     *
     * @param string  $location  name of location to check
     * @param string  $item  name of item that should be in this location
     *
     * @return void
     */
    public function testVanillaItemPlacement(string $location, string $item): void
    {
        $randomizer = new RandomizerService;
        $world = new World(new ItemCollection, [
            'shuffleItems' => false,
        ]);

        $randomizer->shuffleItems($world, new ItemCollection());

        $this->assertTrue($world->getLocation($location)->hasItem(Item::get($item)));
    }

    /**
     * Provide vanilla locations and items.
     *
     * @return array
     */
    public function vanillaProvider(): array
    {
        return [
            ['Item 00', 'Hammer'],
            ['Item 01', 'KeysC0'],
            ['Item 03', 'Slingshot'],
            ['Item 05', 'DeviceF0'],
            ['Item 14', 'Boomerang'],
            ['Item 1d', 'Helmet'],
            ['Item 2a', 'DeviceF2'],
            ['Item 4a', 'JumpShoes'],
            ['Item 46', 'DeviceF3'],
            ['Item 57', 'DeviceF4'],
            ['Item 67', 'FireboxCB'],
            ['Item 6b', 'DeviceF5'],
            ['Item 79', 'HyperShoes'],
            ['Item 20', 'DeviceF1'],
            ['Item 21', 'KeysC2'],
            ['Item 07', 'BombsC4'],
            ['Item 0d', 'FireboxC9'],
            ['Item 0e', 'Transceiver'],
            ['Item 0f', 'Candle'],
            ['Item 25', 'BombsC5'],
            ['Item 39', 'KeysC3'],
            ['Item 3c', 'FireboxC8'],
            ['Item 45', 'BombsC6'],
            ['Item 47', 'KeysC1'],
            ['Item 4b', 'Ladder'],
            ['Item 4c', 'BombsC7'],
            ['Item 56', 'FireboxCA'],
            ['Item e9', 'DivingSuit'],
            ['Item e8', 'Vest'],
            ['Item e7', 'Glasses'],
            ['Item ea', 'HyperShoes'],
            ['Item ec', 'Raincoat'],
        ];
    }

    /**
     * @return void
     */
    public function testLargerWorldFillsNothings(): void
    {
        config(['item.junk.Vest' => 0]);
        $randomizer = new RandomizerService;
        $world = new World(new ItemCollection);

        $randomizer->randomize($world);

        $this->assertContains(Item::get('Nothing'), $world->getItems());
    }
}
