<?php

namespace Tests\Unit\Location;

use App\Rom;
use App\Item;
use App\Room;
use Tests\TestCase;
use Fhaculty\Graph\Graph;
use App\Location\SafeHint;

class SafeHintTest extends TestCase
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
        $location = new SafeHint($graph, 'Location Test', $room);

        $location->writeItem(new Rom);
    }

    /**
     * @return void
     */
    public function testSetRegularItemNotPossible()
    {
        $this->expectException(\Exception::class);

        $item = new Item('ItemNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new SafeHint($graph, 'Location Test', $room);

        $location->setItem($item);
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
        $location = new SafeHint($graph, 'Location Test', $room);

        $location->setItem($item);
    }

    /**
     * @return void
     */
    public function testSetItemHintPossible()
    {
        $item = new Item\Hint('HintNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new SafeHint($graph, 'Location Test', $room);

        $location->setItem($item);

        $this->assertEquals($item, $location->getItem());
    }
}
