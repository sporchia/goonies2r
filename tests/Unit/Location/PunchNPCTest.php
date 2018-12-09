<?php

namespace Tests\Unit\Location;

use App\Item;
use App\Location\PunchNPC;
use App\Room;
use App\Rom;
use Fhaculty\Graph\Graph;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PunchNPCTest extends TestCase
{
    /**
     * Test writing without an item should throw an Exception
     *
     * @return void
     */
    public function testWriteNoItem()
    {
        $this->expectException(\Exception::class);

        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new PunchNPC($graph, 'Location Test', $room);

        $location->writeItem(new Rom);
    }

    /**
     * Test writing without an item should throw an Exception
     *
     * @return void
     */
    public function testWriteItem()
    {
        $graph = new Graph;
        $item = new Item('Test Item', 0x34, 0x45);
        $room = new Room($graph, 'Room Test', 0x04, false);
        $location = new PunchNPC($graph, 'Location Test', $room);
        $location->setItem($item);
        $rom = new Rom;

        $location->writeItem($rom);

        $this->assertEquals([0x34], $rom->read(0x6210));
        $this->assertEquals([0x45], $rom->read(0x6215));
    }
}
