<?php

namespace Tests\Unit\Location;

use App\Item;
use App\Location\Cage;
use App\Room;
use Fhaculty\Graph\Graph;
use Tests\TestCase;

class CageTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetRegularItemNotPossible()
    {
        $this->expectException(\Exception::class);

        $item = new Item('ItemNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Cage($graph, 'Location Test', $room);

        $location->setItem($item);
    }

    /**
     * @return void
     */
    public function testSetItemMustBeGoonie()
    {
        $item = new Item\Goonie('GoonieNE', 0x01);
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new Cage($graph, 'Location Test', $room);

        $location->setItem($item);

        $this->assertEquals($item, $location->getItem());
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
        $location = new Cage($graph, 'Location Test', $room);

        $location->setItem($item);
    }
}
