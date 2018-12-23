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
    public function testUnrandomizedSpoilerEmpty()
    {
        $randomizer = new RandomizerService;

        $this->assertEmpty($randomizer->getSpoiler());
    }

    /**
     * @return void
     */
    public function testTryingToRandomizeWithNoEmptyLocationsFails()
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
     * @return void
     */
    public function testLargerWorldFillsNothings()
    {
        config(['item.junk.Vest' => 0]);
        $randomizer = new RandomizerService;
        $world = new World(new ItemCollection);

        $randomizer->randomize($world);

        $this->assertContains(Item::get('Nothing'), $world->getItems());
    }
}
