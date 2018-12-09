<?php

namespace App\Location;

use App\Item;
use App\Location;
use App\Room;
use App\Rom;
use Fhaculty\Graph\Graph;

/**
 * A Location is any place an Item can be found in game
 */
class Cage extends Location
{
    /**
     * @inheritDoc
     */
    public function __construct(Graph $graph, $id, Room $room)
    {
        parent::__construct($graph, $id, $room);

        $this->setAttribute('graphviz.fillcolor', 'palegreen');
        $this->setAttribute('graphviz.style', 'filled');
    }

    /**
     * @inheritDoc
     */
    public function createEdgeToRoom() : void
    {
        parent::createEdgeToRoom();

        $this->edge->setAttribute('graphviz.color', 'green');
    }

    /**
     * @inheritDoc
     */
    public function setItem(Item $item = null) : Location
    {
        if (!$item instanceof Item\Goonie) {
            throw new \Exception('Invalid Item assignment');
        }

        $this->item = $item;

        $this->setAttribute('graphviz.label', $item->getNiceName());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function writeItem(Rom $rom) : Location
    {
        parent::writeItem($rom);

        $write_byte = $this->item === null ? 0x00 : $this->item->getByte() - 0xe0;

        $this->room->remodelForGoonie($rom, $write_byte);

        return $this;
    }
}
