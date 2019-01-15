<?php

namespace App\Location;

use App\Item;
use App\Location;
use App\Rom;
use App\Room;
use Fhaculty\Graph\Graph;

/**
 * A Location is any place an Item can be found in game.
 */
class Cage extends Location
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Graph $graph, $id, Room $room)
    {
        parent::__construct($graph, $id, $room);

        $room->setCage(true);

        $this->setAttribute('graphviz.fillcolor', 'palegreen');
        $this->setAttribute('graphviz.style', 'filled');
    }

    /**
     * {@inheritdoc}
     */
    public function createEdgeToRoom() : void
    {
        parent::createEdgeToRoom();

        $this->edge->setAttribute('graphviz.color', 'green');
    }

    /**
     * Sets the room for this location.
     *
     * @param \App\Room $room Room to be associated
     *
     * @return $this
     */
    public function setRoom(Room $room) : Location
    {
        $this->room->setCage(false);

        $this->room = $room;

        $room->setCage(true);

        return $this;
    }

    /**
     * Sets the item for this location.
     *
     * @param \App\Item|null $item Item to be placed at this Location
     *
     * @throws \Exception if Item is not valid for this Location type
     *
     * @return $this
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
     * Write the Item to this Location in ROM.
     *
     * @param \App\Rom $rom interface we are going to write to
     *
     * @throws \Exception if no item is set for location
     *
     * @return $this
     */
    public function writeItem(Rom $rom) : Location
    {
        parent::writeItem($rom);

        $write_byte = $this->item === null ? 0x00 : $this->item->getByte() - 0xe0;

        return $this;
    }
}
