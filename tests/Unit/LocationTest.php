<?php

namespace Tests\Unit;

use App\Item;
use App\Location;
use App\Rom;
use App\Room;
use Fhaculty\Graph\Graph;
use Tests\TestCase;

class LocationTest extends TestCase
{
    /**
     * Test setting Item.
     *
     * @return void
     */
    public function testSetItem()
    {
        $item = new Item('ItemNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);

        $location->setItem($item);

        $this->assertEquals($item, $location->getItem());
    }

    /**
     * Test has Item.
     *
     * @return void
     */
    public function testHasSpecificItem()
    {
        $item = new Item('ItemNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);

        $location->setItem($item);

        $this->assertTrue($location->hasItem($item));
    }

    /**
     * Test writing without an item should throw an Exception.
     *
     * @return void
     */
    public function testSetItemGoonieNotPossible()
    {
        $this->expectException(\Exception::class);

        $item = new Item\Goonie('GoonieNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);

        $location->setItem($item);
    }

    /**
     * Test writing without an item should throw an Exception.
     *
     * @return void
     */
    public function testSetItemHintNotPossible()
    {
        $this->expectException(\Exception::class);

        $item = new Item\Hint('HintNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);

        $location->setItem($item);
    }

    /**
     * Test writing without an item should throw an Exception.
     *
     * @return void
     */
    public function testWriteNoItem()
    {
        $this->expectException(\Exception::class);

        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);

        $location->writeItem(new Rom);
    }

    /**
     * @return void
     */
    public function testWriteItem()
    {
        $graph = new Graph;
        $item = new Item('Test Item', 0x34, 0x45);
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);
        $location->setItem($item);
        $rom = new Rom;

        $location->writeItem($rom);

        $this->assertEquals([0x00, 0x45, 0x00, 0x34], $rom->read((0x04 * 4) + Rom::ROOM_DATA_OFFSET, 4));
    }

    /**
     * Test getName.
     *
     * @return void
     */
    public function testGetName()
    {
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);

        $this->assertEquals('Location Test', $location->getName());
    }

    /**
     * Test toString.
     *
     * @return void
     */
    public function testToString()
    {
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Location($graph, 'Location Test', $room);

        $this->assertEquals('Location Test', (string) $location);
    }
}
