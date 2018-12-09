<?php

namespace Tests\Unit\Support;

use App\Item;
use App\Room;
use App\Location;
use App\Support\LocationCollection;
use Fhaculty\Graph\Graph;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationCollectionTest extends TestCase
{
    /**
     * Test creating with non-Item throws exception
     *
     * @return void
     */
    public function testWrongTypeException()
    {
        $this->expectException(\Exception::class);

        $item = new LocationCollection(['hello']);
    }

    /**
     * @return void
     */
    public function testItemInLocations()
    {
        $item = new Item('ItemNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);
        $location->setItem($item);

        $graph = new Graph;
        $room2 = new Room($graph, 'Room Test', 0x04, false);
        $location2 = new Location($graph, 'Location Test 2', $room2);
        $location2->setItem($item);

        $locationCollection = new LocationCollection([$location, $location2]);

        $this->assertTrue($locationCollection->itemInLocations($item, 2));
    }

    /**
     * @return void
     */
    public function testLocationsWithItem()
    {
        $item = new Item('ItemNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);
        $location->setItem($item);

        $graph = new Graph;
        $room2 = new Room($graph, 'Room Test', 0x04, false);
        $location2 = new Location($graph, 'Location Test 2', $room2);

        $locationCollection = new LocationCollection([$location, $location2]);

        $this->assertEquals([$location], array_values($locationCollection->locationsWithItem($item)->all()));
    }

    /**
     * @return void
     */
    public function testNonEmptyLocations()
    {
        $item = new Item('ItemNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);
        $location->setItem($item);

        $graph = new Graph;
        $room2 = new Room($graph, 'Room Test', 0x04, false);
        $location2 = new Location($graph, 'Location Test 2', $room2);

        $locationCollection = new LocationCollection([$location, $location2]);

        $this->assertEquals([$location], array_values($locationCollection->getNonEmptyLocations()->all()));
    }

    /**
     * @return void
     */
    public function testGetItems()
    {
        $item = new Item('ItemNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);
        $location->setItem($item);

        $locationCollection = new LocationCollection([$location]);

        $this->assertEquals([$item], array_values($locationCollection->getItems()->all()));
    }
}
