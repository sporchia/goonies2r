<?php

namespace Tests\Unit;

use App\Room;
use Fhaculty\Graph\Graph;
use Tests\TestCase;

class RoomTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRoomId()
    {
        $graph = new Graph;
        $room = new Room($graph, 'Room Test', 0x04, false);

        $this->assertEquals(0x04, $room->getRoomId());
    }
}
