<?php

namespace Tests\Unit\Location;

use App\Item;
use App\Location\SafeItem;
use App\Rom;
use App\Room;
use Fhaculty\Graph\Graph;
use Tests\TestCase;

class SafeItemTest extends TestCase
{
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
        $location = new SafeItem($graph, 'Location Test', $room);

        $location->writeItem(new Rom);
    }

    /**
     * @return void
     */
    public function testSetRegularItemPossible()
    {
        $item = new Item('ItemNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new SafeItem($graph, 'Location Test', $room);

        $location->setItem($item);

        $this->assertEquals($item, $location->getItem());
    }

    /**
     * @return void
     */
    public function testSetItemBeGoonieNotPossible()
    {
        $this->expectException(\Exception::class);

        $item = new Item\Goonie('GoonieNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new SafeItem($graph, 'Location Test', $room);

        $location->setItem($item);
    }

    /**
     * @return void
     */
    public function testSetItemHintNotPossible()
    {
        $this->expectException(\Exception::class);

        $item = new Item\Hint('HintNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new SafeItem($graph, 'Location Test', $room);

        $location->setItem($item);
    }
}
