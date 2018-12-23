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
class SafeItem extends Location
{
    /** @var int */
    const SAFE_OFFSET = 0x5f6a;
    /** @var int */
    const HINT_OFFSET = 0x5f94;
    /** @var int */
    protected $safe_offset;

    /**
     * {@inheritdoc}
     */
    public function __construct(Graph $graph, $id, Room $room, int $safe_offset = 0x00)
    {
        parent::__construct($graph, $id, $room);

        $this->safe_offset = $safe_offset;
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
     * {@inheritdoc}
     */
    public function writeItem(Rom $rom) : Location
    {
        if (!$this->item) {
            throw new \Exception('No Item set to be written');
        }

        $room_offset = $this->room->getRoomId() * 4 + Rom::ROOM_DATA_OFFSET;
        $rom->write($room_offset + 3, pack('C', 0xe6 + $this->safe_offset));
        $rom->write($room_offset + 1, pack('C', $this->item->getGfxByte()));

        $rom->write(0x5f6a + $this->safe_offset, pack('C', $this->item->getByte()));

        return $this;
    }
}
