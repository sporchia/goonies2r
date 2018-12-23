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
class SafeHint extends Location
{
    /** @var int */
    protected $hint_offset;

    /**
     * {@inheritdoc}
     */
    public function __construct(Graph $graph, $id, Room $room, int $hint_offset = 0x00)
    {
        parent::__construct($graph, $id, $room);

        $this->hint_offset = $hint_offset;
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
        if (!$item instanceof Item\Hint) {
            throw new \Exception('Invalid Item assignment');
        }

        $this->item = $item;

        $this->setAttribute('graphviz.label', $item->getNiceName());

        return $this;
    }

    /**
     * Write the Item to this Location in ROM. Will set Item if passed in, and only write if there is an Item set.
     *
     * @TODO: this is side-affecty
     *
     * @param \App\Rom $rom interface we are going to write to
     *
     * @throws \Exception if no item is set for location
     *
     * @return $this
     */
    public function writeItem(Rom $rom) : Location
    {
        if (!$this->item) {
            throw new \Exception('No Item set to be written');
        }

        $room_offset = $this->room->getRoomId() * 4 + Rom::ROOM_DATA_OFFSET;
        $rom->write($room_offset + 3, pack('C', 0xe6));
        $rom->write($room_offset + 1, pack('C', 0x00));

        $rom->write(0x5f88 + $this->hint_offset, pack('C', $this->room->getRoomId()));
        $rom->write(0x5f94 + $this->hint_offset, pack('C', $this->item->getByte()));

        return $this;
    }
}
