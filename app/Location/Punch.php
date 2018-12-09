<?php

namespace App\Location;

use App\Location;
use App\Room;
use Fhaculty\Graph\Graph;

/**
 * A Location is any place an Item can be found in game
 */
class Punch extends Location
{
    /**
     * @inheritDoc
     */
    public function __construct(Graph $graph, $id, Room $room)
    {
        parent::__construct($graph, $id, $room);

        $this->setAttribute('graphviz.fillcolor', 'lightcyan');
        $this->setAttribute('graphviz.style', 'filled');
    }

    /**
     * @inheritDoc
     */
    public function createEdgeToRoom() : void
    {
        parent::createEdgeToRoom();

        $this->edge->setAttribute('graphviz.color', 'cyan');
    }
}
