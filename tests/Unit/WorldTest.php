<?php

namespace Tests\Unit;

use App\Item;
use App\Room;
use App\Support\ItemCollection;
use App\World;
use Fhaculty\Graph\Graph;
use Graphp\Algorithms\ShortestPath\BreadthFirst;
use Tests\TestCase;

class WorldTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanGetToAnnieWithAllItems(): void
    {
        $world = new World(Item::all());

        $bf = new BreadthFirst($world->getVertex('Start'));

        $this->assertTrue($bf->hasVertex($world->getVertex('Annie')));
    }

    /**
     * @return void
     */
    public function testFirstGoonieOnlyRequiresHammerAndKeys(): void
    {
        $world = new World(new ItemCollection([Item::get('Hammer'), Item::get('KeysC0')]));

        $bf = new BreadthFirst($world->getVertex('Start'));

        $this->assertTrue($bf->hasVertex($world->getVertex('Goonie 1')));
    }

    /**
     * Test new World makes Graph.
     *
     * @return void
     */
    public function testWorldCreatesGraph(): void
    {
        $world = new World(new ItemCollection);

        $this->assertInstanceOf(Graph::class, $world->getGraph());
    }

    /**
     * Test new World has correct number of locations.
     *
     * @return void
     */
    public function testWorldNoPrefilledItems(): void
    {
        $world = new World(new ItemCollection);
        $start = $world->getVertex('Start');

        $this->assertEquals(0, $world->collectItemsFrom($start)->count());
    }

    /**
     * Test new World has correct number of locations.
     *
     * @return void
     */
    public function testWorldAllLocationsEmpty(): void
    {
        $world = new World(new ItemCollection);

        $this->assertEquals(49, $world->getEmptyLocations()->count());
    }

    /**
     * Test new World has correct number of locations.
     *
     * @return void
     */
    public function testWorldCorrectNumberOfLocations(): void
    {
        $world = new World(new ItemCollection);

        $this->assertEquals(49, $world->getLocations()->count());
    }

    /**
     * Test new World has correct number of reachable vertices.
     *
     * @return void
     */
    public function testWorldCorrectNumberOfReachableVertices(): void
    {
        $world = new World(new ItemCollection);

        $start = $world->getVertex('Start');
        $graph = $world->getReachableGraphFromVertex($start);

        $this->assertEquals(67, $graph->getVertices()->count());
    }

    /**
     * Test adding Items increase number of reachable vertices.
     *
     * @return void
     */
    public function testWorldChangereachableVertices(): void
    {
        $world = new World(new ItemCollection);

        $start = $world->getVertex('Start');
        $graph = $world->getReachableGraphFromVertex($start);

        $this->assertEquals(67, $graph->getVertices()->count());

        $world->setItems(new ItemCollection([Item::get('Hammer')]));
        $graph = $world->getReachableGraphFromVertex($start);
        $this->assertEquals(101, $graph->getVertices()->count());
    }

    /**
     * @covers App\World::getLocation
     *
     * @return void
     */
    public function testBadLocationException(): void
    {
        $this->expectException(\OutOfBoundsException::class);

        $world = new World(new ItemCollection);

        $world->getLocation('This test location does not exist');
    }

    /**
     * @covers App\World::getRoom
     *
     * @return void
     */
    public function testBadRoomException(): void
    {
        $this->expectException(\OutOfBoundsException::class);

        $world = new World(new ItemCollection);

        $world->getRoom('This test room does not exist');
    }

    /**
     * @covers App\World::getRoom
     *
     * @return void
     */
    public function testGetRoom(): void
    {
        $world = new World(new ItemCollection);

        $room = $world->getRoom('Room 01');

        $this->assertEquals(0x01, $room->getRoomId());
    }

    /**
     * @covers App\World::getPotentialGoonieRooms
     *
     * @return void
     */
    public function testGetPotentialGoonieRoomsDoesNotReturnFilledRooms(): void
    {
        $world = new World(new ItemCollection);

        $annieCage = $world->getLocation('Annie');
        $annieCage->setItem(Item::get('Annie'));

        $room = $world->getRoom('Room 7c');

        $annieCage->setRoom($room);

        $rooms = $world->getPotentialGoonieRooms()->map(function (Room $room) {
            return $room->getRoomId();
        });

        $this->assertNotContains(0x7c, $rooms);
    }
}
