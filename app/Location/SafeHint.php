<?php

namespace App\Location;

use App\Location;
use App\Item;
use App\Room;
use App\Rom;
use Fhaculty\Graph\Graph;

/**
 * A Location is any place an Item can be found in game
 */
class SafeHint extends Location
{
    /** @var int */
    protected $hint_offset;

    /**
     * {@inheritDoc}
     */
    public function __construct(Graph $graph, $id, Room $room, int $hint_offset = 0x00)
    {
        parent::__construct($graph, $id, $room);

        $this->hint_offset = $hint_offset;
    }

    /**
     * {@inheritDoc}
     */
    public function createEdgeToRoom() : void
    {
        parent::createEdgeToRoom();

        $this->edge->setAttribute('graphviz.color', 'green');
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
