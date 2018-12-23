<?php

namespace Tests\Unit;

use App\Item;
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
    public function testCanGetToAnnieWithAllItems()
    {
        $world = new World(Item::all());

        $bf = new BreadthFirst($world->getVertex('Start'));

        $this->assertTrue($bf->hasVertex($world->getVertex('Annie')));
    }

    /**
     * @return void
     */
    public function testFirstGoonieOnlyRequiresHammerAndKeys()
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
    public function testWorldCreatesGraph()
    {
        $world = new World(new ItemCollection);

        $this->assertInstanceOf(Graph::class, $world->getGraph());
    }

    /**
     * Test new World has correct number of locations.
     *
     * @return void
     */
    public function testWorldNoPrefilledItems()
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
    public function testWorldAllLocationsEmpty()
    {
        $world = new World(new ItemCollection);

        $this->assertEquals(48, $world->getEmptyLocations()->count());
    }

    /**
     * Test new World has correct number of locations.
     *
     * @return void
     */
    public function testWorldCorrectNumberOfLocations()
    {
        $world = new World(new ItemCollection);

        $this->assertEquals(48, $world->getLocations()->count());
    }

    /**
     * Test new World has correct number of reachable vertices.
     *
     * @return void
     */
    public function testWorldCorrectNumberOfReachableVertices()
    {
        $world = new World(new ItemCollection);

        $start = $world->getVertex('Start');
        $graph = $world->getReachableGraphFromVertex($start);

        $this->assertEquals(45, $graph->getVertices()->count());
    }

    /**
     * Test adding Items increase number of reachable vertices.
     *
     * @return void
     */
    public function testWorldChangereachableVertices()
    {
        $world = new World(new ItemCollection);

        $start = $world->getVertex('Start');
        $graph = $world->getReachableGraphFromVertex($start);

        $this->assertEquals(45, $graph->getVertices()->count());

        $world->setItems(new ItemCollection([Item::get('Hammer')]));
        $graph = $world->getReachableGraphFromVertex($start);
        $this->assertEquals(72, $graph->getVertices()->count());
    }

    /**
     * @return void
     */
    public function testBadLocationException()
    {
        $this->expectException(\OutOfBoundsException::class);

        $world = new World(new ItemCollection);

        $start = $world->getLocation('This test location does not exist');
    }
}
