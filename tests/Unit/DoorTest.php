<?php

namespace Tests\Unit;

use App\Door;
use App\Room;
use Fhaculty\Graph\Graph;
use Tests\TestCase;

class DoorTest extends TestCase
{
    /**
     * @return void
     */
    public function testDoorId()
    {
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $door = new Door($graph, 0x01, 0x00, 0x2400, $room);

        $this->assertEquals(0x01, $door->getDoorId());
    }

    /**
     * @return void
     */
    public function testSetRoom()
    {
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);
        $door = new Door($graph, 0x01, 0x00, 0x2400, $room);

        $room2 = new Room($graph, 'Room Test 2', 0x05, false);
        $door->setRoom($room2);

        $this->assertEquals($room2, $door->getRoom());
    }
}
